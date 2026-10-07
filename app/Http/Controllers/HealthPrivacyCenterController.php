<?php

namespace App\Http\Controllers;

use App\Models\Patients;
use App\Models\PatientGuardian;
use App\Services\AccessAuditService;
use App\Services\ImmunizationComplianceService;
use App\Services\PatientAccessService;
use Illuminate\Http\Request;
use Illuminate\View\View;

class HealthPrivacyCenterController extends Controller
{
    public function __construct(
        private readonly PatientAccessService $access,
        private readonly ImmunizationComplianceService $compliance,
        private readonly AccessAuditService $audit,
    ) {
    }

    public function index(Request $request): View
    {
        $user = $request->user();
        $patients = $this->access->accessiblePatients($user)
            ->with(['user:id,name,email,dob', 'guardians.guardianUser:id,name,email'])
            ->orderBy('id')
            ->get();

        $pendingGuardianLinks = PatientGuardian::query()
            ->with('patient.user:id,name')
            ->whereRaw('LOWER(invited_email) = ?', [mb_strtolower($user->email)])
            ->where('is_active', true)
            ->where(function ($query): void {
                $query->whereNull('verified_at')->orWhereNull('approved_at');
            })
            ->get();

        return view('privacy-center.index', compact('patients', 'pendingGuardianLinks'));
    }

    public function show(Request $request, Patients $patient): View
    {
        $actor = $request->user();
        $authorizedScopes = $this->access->authorizedScopes($actor, $patient);
        abort_if($authorizedScopes === [], 403, 'You are not authorized to access this profile.');

        $isSubjectController = $this->access->isOwner($actor, $patient)
            || ($patient->requiresGuardianConsent() && $this->access->isVerifiedGuardian($actor, $patient));

        if (! $isSubjectController) {
            $patient->load([
                'user:id,name,email,dob,contact_no',
                'immunizations.requirement',
                'prescriptions.medicine',
                'appointments',
                'healthRecords',
            ]);

            $authorizations = $patient->dataAuthorizations()
                ->active()
                ->where(function ($query) use ($actor): void {
                    $query->where('recipient_user_id', $actor->id)
                        ->orWhereRaw('LOWER(recipient_email) = ?', [mb_strtolower($actor->email)]);
                })
                ->latest('id')
                ->get();

            $this->audit->record($actor, $patient, 'authorized_record_viewed', 'consent', $authorizedScopes, 'View data under active digital authorization', [], $request);

            return view('privacy-center.authorized-view', compact('patient', 'authorizedScopes', 'authorizations'));
        }

        $patient->load([
            'user:id,name,email,dob,contact_no',
            'guardians.guardianUser:id,name,email',
            'dataAuthorizations' => fn ($query) => $query->latest(),
            'immunizations.requirement',
        ]);

        $compliance = $this->compliance->forPatient($patient);
        $canManageConsent = $this->access->canManageConsent($actor, $patient);
        $canUploadImmunization = $this->access->canUploadImmunization($actor, $patient);
        $scopes = config('health_privacy.scopes', []);

        return view('privacy-center.show', compact(
            'patient', 'compliance', 'canManageConsent', 'canUploadImmunization', 'scopes'
        ));
    }
}
