<?php

namespace App\Http\Middleware;

use App\Models\User;
use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class EnsurePatientSelfService
{
    public function handle(Request $request, Closure $next): Response
    {
        abort_unless(
            $request->user() && in_array($request->user()->user_type, [User::TYPE_STUDENT, User::TYPE_FACULTY], true),
            403,
            'This route is limited to the patient self-service portal.'
        );

        return $next($request);
    }
}
