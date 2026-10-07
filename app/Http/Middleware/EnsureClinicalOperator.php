<?php

namespace App\Http\Middleware;

use App\Models\User;
use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class EnsureClinicalOperator
{
    public function handle(Request $request, Closure $next): Response
    {
        $user = $request->user();

        abort_unless(
            $user && in_array($user->user_type, [User::TYPE_STAFF, User::TYPE_DOCTOR], true),
            403,
            'This clinical operation is limited to school nurses and doctors.'
        );

        return $next($request);
    }
}
