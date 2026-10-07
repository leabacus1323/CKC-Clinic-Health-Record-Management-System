<?php

namespace App\Http\Middleware;

use Illuminate\Auth\Middleware\Authenticate as Middleware;

class Authenticate extends Middleware
{
    protected function redirectTo($request)
    {
        // Default redirect for non-authenticated users to base URL
        if (!$request->expectsJson()) {
            return '/'; // Redirect to base URL
        }
    }
}
