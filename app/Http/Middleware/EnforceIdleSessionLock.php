<?php

namespace App\Http\Middleware;

use App\Services\MedicalAuditService;
use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class EnforceIdleSessionLock
{
    public function __construct(private readonly MedicalAuditService $audit)
    {
    }

    public function handle(Request $request, Closure $next): Response
    {
        $user = $request->user();
        if (! $user) {
            return $next($request);
        }

        $routeName = (string) ($request->route()?->getName() ?? '');
        if (in_array($routeName, [
            'security.lock.show', 'security.lock.store', 'security.lock.unlock',
            'logout', 'privacy-agreement.show', 'privacy-agreement.accept', 'privacy-agreement.decline',
        ], true)) {
            return $next($request);
        }

        $timeout = max(120, (int) config('medical_security.idle_timeout_seconds', 180));
        $lastActivity = (int) $request->session()->get('security_last_activity_at', now()->timestamp);
        $isLocked = (bool) $request->session()->get('security_session_locked', false);

        if (! $isLocked && now()->timestamp - $lastActivity >= $timeout) {
            $request->session()->put([
                'security_session_locked' => true,
                'security_locked_at' => now()->toIso8601String(),
                'url.intended' => $request->fullUrl(),
            ]);

            $this->audit->record($user, 'READ', 'session_locked_for_inactivity', null, [
                'resource_type' => 'AuthenticatedSession',
                'resource_id' => $request->session()->getId(),
            ], $request);

            $isLocked = true;
        }

        if ($isLocked) {
            if ($request->expectsJson()) {
                return response()->json([
                    'message' => 'Session locked because of inactivity.',
                    'redirect' => route('security.lock.show'),
                ], 423);
            }

            return redirect()->route('security.lock.show');
        }

        /*
         * Do not treat every request as user activity. Dashboards may poll in the
         * background, and those requests must not keep an unattended terminal
         * unlocked. The explicit activity heartbeat updates the timestamp in its
         * controller. Here we refresh only for deliberate form mutations or a
         * top-level browser document navigation.
         */
        if ($this->isDeliberateUserRequest($request)) {
            $request->session()->put('security_last_activity_at', now()->timestamp);
        }

        return $next($request);
    }

    private function isDeliberateUserRequest(Request $request): bool
    {
        if (in_array($request->method(), ['POST', 'PUT', 'PATCH', 'DELETE'], true)
            && ! $request->routeIs('security.activity')) {
            return true;
        }

        if ($request->method() !== 'GET') {
            return false;
        }

        if ($request->ajax() || $request->expectsJson()) {
            return false;
        }

        $fetchDestination = strtolower((string) $request->header('Sec-Fetch-Dest'));

        return $fetchDestination === 'document'
            || ($fetchDestination === '' && $request->acceptsHtml());
    }
}
