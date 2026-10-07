<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class EnsurePrivacyAgreementAccepted
{
    /**
     * Prevent authenticated users from entering protected pages until they
     * have accepted the current privacy agreement version.
     */
    public function handle(Request $request, Closure $next): Response
    {
        $user = $request->user();

        if ($user && ! $user->hasAcceptedCurrentPrivacyTerms()) {
            if ($request->expectsJson()) {
                return response()->json([
                    'message' => 'The current Data Privacy Agreement must be accepted before continuing.',
                    'redirect' => route('privacy-agreement.show'),
                ], 403);
            }

            $request->session()->put(
                'url.intended',
                $request->isMethod('GET') ? $request->fullUrl() : route('dashboard')
            );

            return redirect()->route('privacy-agreement.show');
        }

        return $next($request);
    }
}
