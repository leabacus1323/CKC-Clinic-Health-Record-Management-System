<?php

namespace App\Http\Controllers;

use App\Models\InfrastructureEvent;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\View\View;

class AdminSystemController extends Controller
{
    public function index(Request $request): View
    {
        $role = $request->string('role', 'clinical')->toString();
        $allowedRoles = ['clinical', 'staff', 'doctor', 'admin', 'all'];
        if (! in_array($role, $allowedRoles, true)) {
            $role = 'clinical';
        }

        $eventsQuery = InfrastructureEvent::query()
            ->with('actor:id,name,email,user_type')
            ->latest('occurred_at');

        $this->applyRoleFilter($eventsQuery, $role);

        if ($request->filled('category')) {
            $eventsQuery->where('event_category', $request->string('category')->toString());
        }

        if ($request->filled('status')) {
            $status = $request->string('status')->toString();
            if ($status === 'success') {
                $eventsQuery->whereBetween('response_status', [200, 399]);
            } elseif ($status === 'failed') {
                $eventsQuery->where('response_status', '>=', 400);
            }
        }

        if ($request->filled('from')) {
            $eventsQuery->whereDate('occurred_at', '>=', $request->date('from'));
        }

        if ($request->filled('to')) {
            $eventsQuery->whereDate('occurred_at', '<=', $request->date('to'));
        }

        if ($request->filled('search')) {
            $search = trim($request->string('search')->toString());
            $eventsQuery->where(function ($query) use ($search): void {
                $query->where('route_name', 'like', "%{$search}%")
                    ->orWhere('description', 'like', "%{$search}%")
                    ->orWhere('ip_address', 'like', "%{$search}%")
                    ->orWhereHas('actor', function ($actorQuery) use ($search): void {
                        $actorQuery->where('name', 'like', "%{$search}%")
                            ->orWhere('email', 'like', "%{$search}%");
                    });
            });
        }

        $events = $eventsQuery->paginate(50)->withQueryString();

        $clinicalEvents = InfrastructureEvent::query()
            ->whereIn('actor_user_type', [User::TYPE_STAFF, User::TYPE_DOCTOR]);

        $summary = [
            'staff' => (clone $clinicalEvents)->where('actor_user_type', User::TYPE_STAFF)->count(),
            'doctors' => (clone $clinicalEvents)->where('actor_user_type', User::TYPE_DOCTOR)->count(),
            'today' => (clone $clinicalEvents)->whereDate('occurred_at', today())->count(),
            'failed' => (clone $clinicalEvents)->where('response_status', '>=', 400)->count(),
        ];

        $categories = InfrastructureEvent::query()
            ->whereNotNull('event_category')
            ->distinct()
            ->orderBy('event_category')
            ->pluck('event_category');

        $system = [
            'environment' => app()->environment(),
            'php_version' => PHP_VERSION,
            'laravel_version' => app()->version(),
            'database_driver' => DB::connection()->getDriverName(),
            'cache_store' => config('cache.default'),
            'session_driver' => config('session.driver'),
            'queue_connection' => config('queue.default'),
            'idle_lock_seconds' => config('medical_security.idle_timeout_seconds'),
            'medical_audit_driver' => config('medical_security.audit.driver'),
            'medical_audit_fail_closed' => config('medical_security.audit.fail_closed'),
            'debug_enabled' => config('app.debug'),
        ];

        return view('admin.system.index', compact(
            'events', 'system', 'summary', 'categories', 'role'
        ));
    }

    private function applyRoleFilter($query, string $role): void
    {
        match ($role) {
            'clinical' => $query->whereIn('actor_user_type', [User::TYPE_STAFF, User::TYPE_DOCTOR]),
            'staff' => $query->where('actor_user_type', User::TYPE_STAFF),
            'doctor' => $query->where('actor_user_type', User::TYPE_DOCTOR),
            'admin' => $query->where('actor_user_type', User::TYPE_ADMIN),
            default => null,
        };
    }
}
