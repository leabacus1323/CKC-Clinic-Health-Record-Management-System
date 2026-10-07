<?php

namespace App\Http\Middleware;

use App\Models\InfrastructureEvent;
use App\Models\User;
use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Str;
use Symfony\Component\HttpFoundation\Response;
use Throwable;

class RecordInfrastructureActivity
{
    public function handle(Request $request, Closure $next): Response
    {
        $response = $next($request);
        $user = $request->user();

        if (! $user || ! in_array($user->user_type, [User::TYPE_ADMIN, User::TYPE_STAFF, User::TYPE_DOCTOR], true)) {
            return $response;
        }

        $routeName = (string) ($request->route()?->getName() ?? 'unnamed');

        // Avoid filling the activity store with background polling and AJAX panel refreshes.
        if ($this->shouldSkip($routeName)) {
            return $response;
        }

        try {
            if (! Schema::hasTable('infrastructure_events')) {
                return $response;
            }

            $payload = [
                'actor_user_id' => $user->id,
                'action' => $this->action($request),
                'route_name' => $routeName,
                'request_method' => $request->method(),
                'response_status' => $response->getStatusCode(),
                'ip_address' => $request->ip(),
                'request_id' => (string) Str::ulid(),
                'occurred_at' => now(),
            ];

            if (Schema::hasColumn('infrastructure_events', 'actor_user_type')) {
                $payload['actor_user_type'] = $user->user_type;
                $payload['actor_role'] = $user->roleLabel();
                $payload['event_category'] = $this->category($routeName);
                $payload['description'] = $this->description($request, $routeName);
                $payload['user_agent'] = Str::limit((string) $request->userAgent(), 255, '');
            }

            InfrastructureEvent::create($payload);
        } catch (Throwable $exception) {
            // Logging must never make the clinic portal unavailable.
            Log::warning('Unable to record infrastructure activity.', [
                'route' => $routeName,
                'actor_user_id' => $user->id,
                'error' => $exception->getMessage(),
            ]);
        }

        return $response;
    }

    private function shouldSkip(string $routeName): bool
    {
        return $routeName === 'notifications.get'
            || Str::contains($routeName, '.ajax.');
    }

    private function action(Request $request): string
    {
        return match ($request->method()) {
            'GET', 'HEAD', 'OPTIONS' => 'READ',
            'POST' => 'CREATE',
            'PUT', 'PATCH' => 'UPDATE',
            'DELETE' => 'DELETE',
            default => 'EXECUTE',
        };
    }

    private function category(string $routeName): string
    {
        return match (true) {
            Str::contains($routeName, 'appointment') => 'appointments',
            Str::contains($routeName, ['health-record', 'physical-exam', 'dental-exam']) => 'clinical-records',
            Str::contains($routeName, 'prescription') => 'prescriptions',
            Str::contains($routeName, ['medicine', 'medication']) => 'medicines',
            Str::contains($routeName, 'immunization') => 'immunizations',
            Str::contains($routeName, 'certificate') => 'certificates',
            Str::contains($routeName, 'announcement') => 'announcements',
            Str::contains($routeName, ['report', 'analytic', 'statistic']) => 'reports',
            Str::contains($routeName, ['infrastructure', 'admin.system']) => 'infrastructure',
            Str::contains($routeName, ['user', 'doctor', 'patient']) => 'accounts-and-patients',
            default => 'portal',
        };
    }

    private function description(Request $request, string $routeName): string
    {
        $verb = match ($this->action($request)) {
            'READ' => 'Viewed',
            'CREATE' => 'Created or submitted',
            'UPDATE' => 'Updated',
            'DELETE' => 'Deleted',
            default => 'Executed',
        };

        $subject = Str::of($routeName)
            ->replace(['staff.', 'doctor.', 'admin.'], '')
            ->replace(['.', '-'], ' ')
            ->headline()
            ->lower();

        return Str::limit("{$verb} {$subject}", 255, '');
    }
}
