<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\View\View;

class EmailVerificationPromptController extends Controller
{
    /**
     * Display the email verification prompt.
     */
    public function __invoke(Request $request): RedirectResponse|View
    {
        if ($request->user()->hasVerifiedEmail()) {
            $user = Auth::user();
            switch ($user->user_type) {
                case 1:
                    return redirect()->intended(route('patients.dashboard'));
                case 2:
                    return redirect()->intended(route('staff.dashboard'));
                case 3:
                    return redirect()->intended(route('doctor.dashboard'));
                default:
                    return redirect()->intended(route('patients.dashboard'));
            }
        }
        return view('auth.verify-email');
    }
}
