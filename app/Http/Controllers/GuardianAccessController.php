<?php

namespace App\Http\Controllers;

use App\Models\PatientGuardian;
use App\Models\Patients;
use App\Models\User;
use App\Services\AccessAuditService;
use App\Services\PatientAccessService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

class GuardianAccessController extends Controller
{
    public function __construct(
        private readonly PatientAccessService $access,
        private readonly AccessAuditService $audit,
    ) {
    }

    public function store(Request $request, Patients $patient): RedirectResponse
    {
        abort_unless($this->access->isOwner($request->user(), $patient), 403);
        abort_unless($patient->requiresGuardianConsent(), 422, 'Automatic guardian access is available only while this student profile is guardian-controlled. Use a digital authorization after rights transfer.');

        $data = $request->validate([
            'full_name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'email', 'max:255', Rule::notIn([$request->user()->email])],
            'relationship' => ['required', 'string', 'max:80'],
            'can_manage_consents' => ['nullable', 'boolean'],
        ]);

        $guardianUser = User::query()
            ->whereRaw('LOWER(email) = ?', [mb_strtolower($data['email'])])
            ->first();

        if ($guardianUser && $guardianUser->user_type !== User::TYPE_GUARDIAN) {
            return back()->withErrors(['email' => 'This email belongs to a non-guardian account. Ask an administrator to correct the account type.']);
        }

        $link = PatientGuardian::updateOrCreate(
            ['patient_id' => $patient->id, 'invited_email' => mb_strtolower($data['email'])],
            [
                'guardian_user_id' => $guardianUser?->id,
                'full_name' => $data['full_name'],
                'relationship' => $data['relationship'],
                'can_manage_consents' => (bool) ($data['can_manage_consents'] ?? false),
                'is_active' => true,
                'verified_at' => null,
                'approved_at' => null,
                'approved_by_user_id' => null,
                'approval_notes' => null,
                'created_by_user_id' => $request->user()->id,
            ]
        );

        $this->audit->record($request->user(), $patient, 'guardian_invited', 'consent', ['identity'], 'Establish guardian portal access', [
            'guardian_link_id' => $link->id,
            'invited_email' => $link->invited_email,
        ], $request);

        return back()->with('success', 'Guardian invitation saved. The guardian can accept after signing in with the same email address.');
    }

    public function accept(Request $request, PatientGuardian $guardianLink): RedirectResponse
    {
        $user = $request->user();
        abort_unless($user->user_type === User::TYPE_GUARDIAN, 403);
        abort_unless(mb_strtolower($guardianLink->invited_email) === mb_strtolower($user->email), 403);
        abort_if(! $guardianLink->is_active, 410, 'This guardian invitation is no longer active.');

        $guardianLink->update([
            'guardian_user_id' => $user->id,
            'verified_at' => now(),
        ]);

        $this->audit->record($user, $guardianLink->patient, 'guardian_access_accepted', 'consent', ['identity'], 'Accept guardian portal access', [
            'guardian_link_id' => $guardianLink->id,
        ], $request);

        return redirect()->route('privacy-center.index')
            ->with('success', 'Guardian account accepted. Access will begin after a clinic administrator verifies the legal relationship.');
    }

    public function destroy(Request $request, Patients $patient, PatientGuardian $guardianLink): RedirectResponse
    {
        abort_unless($guardianLink->patient_id === $patient->id, 404);
        abort_unless($this->access->isOwner($request->user(), $patient), 403);

        $guardianLink->update(['is_active' => false]);
        $this->audit->record($request->user(), $patient, 'guardian_access_disabled', 'consent', [], 'Disable guardian portal access', [
            'guardian_link_id' => $guardianLink->id,
        ], $request);

        return back()->with('success', 'Guardian access disabled.');
    }
}
