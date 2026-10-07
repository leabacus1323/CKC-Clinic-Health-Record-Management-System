<?php

namespace App\Http\Middleware;

use App\Models\User;
use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class EnsureTeacherSafetyAccess
{
    public function handle(Request $request, Closure $next): Response
    {
        abort_unless(
            $request->user()?->user_type === User::TYPE_TEACHER,
            403,
            'Teachers may access only the read-only life-safety portal.'
        );

        return $next($request);
    }
}
