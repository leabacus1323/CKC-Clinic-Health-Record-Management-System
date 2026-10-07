<?php

namespace App\Http\Middleware;

use App\Models\User;
use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class EnsureSystemAdministrator
{
    public function handle(Request $request, Closure $next): Response
    {
        abort_unless(
            $request->user()?->user_type === User::TYPE_ADMIN,
            403,
            'This area is limited to system administrators.'
        );

        return $next($request);
    }
}
