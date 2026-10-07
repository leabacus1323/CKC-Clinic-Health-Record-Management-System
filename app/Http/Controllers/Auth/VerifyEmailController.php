<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use Illuminate\Auth\Events\Verified;
use Illuminate\Foundation\Auth\EmailVerificationRequest;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\Auth;

class VerifyEmailController extends Controller
{
    /**
     * Mark the authenticated user's email address as verified.
     */
    public function __invoke(EmailVerificationRequest $request): RedirectResponse
    {
        if ($request->user()->hasVerifiedEmail()) {
            $user = Auth::user();
            switch ($user->user_type) {
                case 1:
                    return redirect()->intended(route('patients.dashboard').'?verified=1');
                case 2:
                    return redirect()->intended(route('staff.dashboard').'?verified=1');
                case 3:
                    return redirect()->intended(route('doctor.dashboard').'?verified=1');
                default:
                    return redirect()->intended(route('patients.dashboard').'?verified=1');
            }
        }

        if ($request->user()->markEmailAsVerified()) {
            event(new Verified($request->user()));
        }

        $user = Auth::user();
        switch ($user->user_type) {
            case 1:
                return redirect()->intended(route('patients.dashboard').'?verified=1');
            case 2:
                return redirect()->intended(route('staff.dashboard').'?verified=1');
            case 3:
                return redirect()->intended(route('doctor.dashboard').'?verified=1');
            default:
                return redirect()->intended(route('patients.dashboard').'?verified=1');
        }
    }
}
