<?php

namespace App\Http\Middleware;

use App\Models\User;
use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class EnsureEmergencyAdministrator
{
    public function handle(Request $request, Closure $next): Response
    {
        $user = $request->user();

        abort_unless(
            $user && $user->user_type === User::TYPE_PRINCIPAL,
            403,
            'This emergency console is limited to the designated Principal / Emergency Administrator.'
        );

        return $next($request);
    }
}
