<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class EmailVerificationNotificationController extends Controller
{
    /**
     * Send a new email verification notification.
     */
    public function store(Request $request): RedirectResponse
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

        $request->user()->sendEmailVerificationNotification();

        return back()->with('status', 'verification-link-sent');
    }
}
