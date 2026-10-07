<?php

namespace App\Http\Controllers;

use App\Models\DataAuthorization;
use App\Models\Patients;
use App\Models\User;
use App\Services\AccessAuditService;
use App\Services\PatientAccessService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Illuminate\Validation\Rule;
use Illuminate\View\View;

class DataAuthorizationController extends Controller
{
    public function __construct(
        private readonly PatientAccessService $access,
        private readonly AccessAuditService $audit,
    ) {
    }

    public function store(Request $request, Patients $patient): RedirectResponse
    {
        abort_unless($this->access->canManageConsent($request->user(), $patient), 403);

        $availableScopes = array_keys(config('health_privacy.scopes', []));
        $maxDate = now()->addMonths((int) config('health_privacy.consent_max_months', 24))->toDateString();

        $data = $request->validate([
            'recipient_name' => ['required', 'string', 'max:255'],
            'recipient_email' => ['required', 'email', 'max:255'],
            'scopes' => ['required', 'array', 'min:1'],
            'scopes.*' => ['required', Rule::in($availableScopes)],
            'purpose' => ['required', 'string', 'min:10', 'max:2000'],
            'expires_at' => ['required', 'date', 'after:today', 'before_or_equal:'.$maxDate],
            'signature_name' => ['required', 'string', 'max:255'],
            'current_password' => ['required', 'current_password'],
            'affirmation' => ['accepted'],
            'revocation_acknowledgement' => ['accepted'],
            'redisclosure_acknowledgement' => ['accepted'],
        ]);

        $scopes = array_values(array_unique($data['scopes']));
        sort($scopes);
        $authorizationVersion = (string) config('health_privacy.authorization_version', '1.0');

        $recipient = User::query()
            ->whereRaw('LOWER(email) = ?', [mb_strtolower($data['recipient_email'])])
            ->first();
        $signedAt = now()->startOfSecond();
        $publicId = (string) Str::uuid();
        $scopeLabels = collect($scopes)
            ->map(fn (string $scope): string => (string) config('health_privacy.scopes.'.$scope, $scope))
            ->implode('; ');
        $authorizationText = implode("\n", [
            'I authorize the disclosure of the following health-information categories to '.$data['recipient_name'].' <'.mb_strtolower($data['recipient_email']).'>: '.$scopeLabels.'.',
            'Purpose: '.$data['purpose'],
            'This authorization expires on '.$data['expires_at'].'.',
            'I may revoke this authorization through the portal, except to the extent action has already been taken in reliance on it.',
            'Information disclosed to the named recipient may be subject to redisclosure under applicable law.',
            'Signing is not a condition of treatment or enrollment except where applicable law expressly permits it.',
        ]);
        $signaturePayload = implode('|', [
            $publicId,
            $patient->id,
            $request->user()->id,
            mb_strtolower($data['recipient_email']),
            json_encode($scopes),
            $data['purpose'],
            $signedAt->format('Y-m-d H:i:s'),
            $request->ip(),
            $authorizationVersion,
            hash('sha256', $authorizationText),
        ]);

        $authorization = DataAuthorization::create([
            'public_id' => $publicId,
            'patient_id' => $patient->id,
            'grantor_user_id' => $request->user()->id,
            'recipient_user_id' => $recipient?->id,
            'recipient_name' => $data['recipient_name'],
            'recipient_email' => mb_strtolower($data['recipient_email']),
            'scopes' => $scopes,
            'purpose' => $data['purpose'],
            'signature_name' => $data['signature_name'],
            'signature_hash' => hash_hmac('sha256', $signaturePayload, (string) config('app.key')),
            'authorization_version' => $authorizationVersion,
            'authorization_text' => $authorizationText,
            'status' => 'active',
            'effective_at' => $signedAt,
            'expires_at' => $data['expires_at'],
            'signed_ip' => $request->ip(),
            'signed_user_agent' => mb_substr((string) $request->userAgent(), 0, 2000),
        ]);

        $this->audit->record($request->user(), $patient, 'authorization_signed', 'consent', $authorization->scopes, $authorization->purpose, [
            'authorization_public_id' => $authorization->public_id,
            'recipient_email' => $authorization->recipient_email,
            'expires_at' => $authorization->expires_at?->toISOString(),
            'authorization_version' => $authorization->authorization_version,
        ], $request);

        return back()->with('success', 'The digital authorization was signed and activated.');
    }

    public function show(Request $request, Patients $patient, DataAuthorization $authorization): View
    {
        abort_unless($authorization->patient_id === $patient->id, 404);

        $actor = $request->user();
        $isRecipient = (int) $authorization->recipient_user_id === (int) $actor->id
            || mb_strtolower($authorization->recipient_email) === mb_strtolower($actor->email);
        $isParty = (int) $authorization->grantor_user_id === (int) $actor->id
            || $isRecipient
            || $this->access->isOwner($actor, $patient)
            || $this->access->isVerifiedGuardian($actor, $patient);

        abort_unless($isParty, 403);

        $authorization->load(['grantor:id,name,email', 'recipient:id,name,email']);
        $patient->load('user:id,name,email');

        $this->audit->record(
            $actor,
            $patient,
            'authorization_form_viewed',
            'consent',
            $authorization->scopes,
            'Review signed electronic authorization',
            ['authorization_public_id' => $authorization->public_id],
            $request,
        );

        return view('privacy-center.authorization', compact('patient', 'authorization'));
    }

    public function revoke(Request $request, Patients $patient, DataAuthorization $authorization): RedirectResponse
    {
        abort_unless($authorization->patient_id === $patient->id, 404);
        abort_unless($this->access->canManageConsent($request->user(), $patient), 403);

        $data = $request->validate([
            'revocation_reason' => ['required', 'string', 'min:5', 'max:1000'],
        ]);

        if ($authorization->status !== 'active') {
            return back()->with('info', 'This authorization is already inactive.');
        }

        $authorization->update([
            'status' => 'revoked',
            'revoked_at' => now(),
            'revoked_by_user_id' => $request->user()->id,
            'revocation_reason' => $data['revocation_reason'],
        ]);

        $this->audit->record($request->user(), $patient, 'authorization_revoked', 'consent', $authorization->scopes, $data['revocation_reason'], [
            'authorization_public_id' => $authorization->public_id,
        ], $request);

        return back()->with('success', 'Authorization revoked immediately.');
    }
}
