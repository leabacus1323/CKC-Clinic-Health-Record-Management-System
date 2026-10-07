<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\User;

class CheckUserType
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure  $next
     * @param  int  $userType
     * @return mixed
     */
    public function handle(Request $request, Closure $next, $userType)
    {
        // Check 'admin' guard first (for admin login)
        if (Auth::guard('admin')->check()) {
            $user = Auth::guard('admin')->user();
            if ($user->user_type == $userType) {
                return $next($request);
            }
            return $this->denyAccess($user->user_type);
        }

        // Also check default guard (for other login methods)
        if (Auth::check()) {
            $user = Auth::user();
            if ($user->user_type == $userType) {
                return $next($request);
            }
            return $this->denyAccess($user->user_type);
        }

        // Not logged in - redirect to login
        return redirect()->route('login');
    }

    private function denyAccess($userType)
    {
        switch ($userType) {
            case User::TYPE_ADMIN:
                return redirect()->route('admin.dashboard')->with('error', 'Access denied.');
            case User::TYPE_STUDENT:
                return redirect()->route('patients.dashboard')->with('error', 'Access denied.');
            case User::TYPE_STAFF:
                return redirect()->route('staff.dashboard')->with('error', 'Access denied.');
            case User::TYPE_DOCTOR:
                return redirect()->route('doctor.dashboard')->with('error', 'Access denied.');
            case User::TYPE_TEACHER:
                return redirect()->route('teacher.safety.index')->with('error', 'Access denied.');
            case User::TYPE_GUARDIAN:
                return redirect()->route('privacy-center.index')->with('error', 'Access denied.');
            case User::TYPE_PRINCIPAL:
                return redirect()->route('break-glass.index')->with('error', 'Access denied.');
            default:
                return redirect()->route('login')->with('error', 'Access denied.');
        }
    }
}
