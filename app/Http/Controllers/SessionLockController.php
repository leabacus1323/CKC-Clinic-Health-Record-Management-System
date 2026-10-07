<?php

namespace App\Http\Controllers;

use App\Services\MedicalAuditService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\ValidationException;
use Illuminate\View\View;
use Symfony\Component\HttpFoundation\Response;

class SessionLockController extends Controller
{
    public function __construct(private readonly MedicalAuditService $audit)
    {
    }

    public function show(Request $request): View|RedirectResponse
    {
        if (! $request->session()->get('security_session_locked', false)) {
            return redirect()->route('dashboard');
        }

        return view('security.lock-screen', [
            'lockedAt' => $request->session()->get('security_locked_at'),
        ]);
    }

    public function lock(Request $request): RedirectResponse
    {
        $request->session()->put([
            'security_session_locked' => true,
            'security_locked_at' => now()->toIso8601String(),
            'url.intended' => url()->previous(),
        ]);

        $this->audit->record($request->user(), 'READ', 'session_locked', null, [
            'resource_type' => 'AuthenticatedSession',
            'resource_id' => $request->session()->getId(),
        ], $request);

        return redirect()->route('security.lock.show');
    }

    public function touch(Request $request): Response
    {
        // Only this endpoint is called by the real browser input listeners. Background
        // dashboard polling therefore cannot keep an unattended terminal unlocked.
        $request->session()->put('security_last_activity_at', now()->timestamp);

        return response()->noContent();
    }

    public function unlock(Request $request): RedirectResponse
    {
        $data = $request->validate(['password' => ['required', 'string']]);

        if (! Hash::check($data['password'], (string) $request->user()->password)) {
            throw ValidationException::withMessages([
                'password' => __('The password is incorrect.'),
            ]);
        }

        $request->session()->forget(['security_session_locked', 'security_locked_at']);
        $request->session()->put('security_last_activity_at', now()->timestamp);
        $request->session()->regenerate();

        $this->audit->record($request->user(), 'READ', 'session_unlocked', null, [
            'resource_type' => 'AuthenticatedSession',
            'resource_id' => $request->session()->getId(),
        ], $request);

        return redirect()->intended(route('dashboard'));
    }
}
