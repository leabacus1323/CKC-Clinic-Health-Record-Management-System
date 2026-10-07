<?php

namespace App\Http\Controllers;

use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\View\View;

class PrivacyAgreementController extends Controller
{
    /**
     * Display the current Data Privacy Agreement and Terms of Use.
     */
    public function show(Request $request): View|RedirectResponse
    {
        $user = $request->user();

        if ($user->hasAcceptedCurrentPrivacyTerms()) {
            return redirect()->intended(route('dashboard'));
        }

        return view('auth.privacy-agreement', [
            'termsVersion' => (string) config('privacy.terms_version'),
            'effectiveDate' => (string) config('privacy.effective_date'),
            'organization' => (string) config('privacy.organization'),
            'contactEmail' => config('privacy.contact_email'),
        ]);
    }

    /**
     * Record acceptance and continue to the user's intended page.
     */
    public function accept(Request $request): RedirectResponse
    {
        $request->validate([
            'agreement' => ['accepted'],
        ], [
            'agreement.accepted' => 'You must agree to the Data Privacy Agreement and Terms of Use to continue.',
        ]);

        $request->user()->forceFill([
            'privacy_terms_version' => (string) config('privacy.terms_version'),
            'privacy_terms_accepted_at' => now(),
            'privacy_terms_accepted_ip' => $request->ip(),
            'privacy_terms_accepted_user_agent' => mb_substr((string) $request->userAgent(), 0, 1000),
        ])->save();

        $request->session()->regenerateToken();

        return redirect()
            ->intended(route('dashboard'))
            ->with('success', 'Thank you. Your privacy agreement has been recorded.');
    }

    /**
     * Decline the agreement, terminate the authenticated session, and return to login.
     */
    public function decline(Request $request): RedirectResponse
    {
        Auth::guard('web')->logout();

        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect()
            ->route('login')
            ->with('status', 'You declined the Data Privacy Agreement. Please agree to the terms before accessing your account.');
    }
}
