<?php

namespace App\Services;

use App\Models\DataAuthorization;
use App\Models\Patients;
use App\Models\PatientGuardian;
use App\Models\User;

class PatientAccessService
{
    public function isOwner(User $actor, Patients $patient): bool
    {
        return (int) $patient->user_id === (int) $actor->id;
    }

    public function isVerifiedGuardian(User $actor, Patients $patient): bool
    {
        return PatientGuardian::query()
            ->where('patient_id', $patient->id)
            ->where('guardian_user_id', $actor->id)
            ->where('is_active', true)
            ->whereNotNull('verified_at')
            ->whereNotNull('approved_at')
            ->exists();
    }

    public function canManageConsent(User $actor, Patients $patient): bool
    {
        if ($this->isOwner($actor, $patient)) {
            return ! $patient->requiresGuardianConsent();
        }

        if (! $patient->requiresGuardianConsent()) {
            return false;
        }

        return PatientGuardian::query()
            ->where('patient_id', $patient->id)
            ->where('guardian_user_id', $actor->id)
            ->where('is_active', true)
            ->where('can_manage_consents', true)
            ->whereNotNull('verified_at')
            ->whereNotNull('approved_at')
            ->exists();
    }

    public function canUploadImmunization(User $actor, Patients $patient): bool
    {
        return $this->isOwner($actor, $patient)
            || ($patient->requiresGuardianConsent() && $this->isVerifiedGuardian($actor, $patient));
    }

    public function canViewScope(User $actor, Patients $patient, string $scope): bool
    {
        if (in_array($actor->user_type, [User::TYPE_ADMIN, User::TYPE_PRINCIPAL, User::TYPE_TEACHER], true)) {
            return false;
        }

        if ($this->isOwner($actor, $patient)) {
            return true;
        }

        if (in_array($actor->user_type, [User::TYPE_DOCTOR, User::TYPE_STAFF], true)) {
            return true;
        }

        if ($patient->requiresGuardianConsent() && $this->isVerifiedGuardian($actor, $patient)) {
            return true;
        }

        return DataAuthorization::query()
            ->active()
            ->where('patient_id', $patient->id)
            ->where(function ($query) use ($actor): void {
                $query->where('recipient_user_id', $actor->id)
                    ->orWhereRaw('LOWER(recipient_email) = ?', [mb_strtolower($actor->email)]);
            })
            ->whereJsonContains('scopes', $scope)
            ->exists();
    }

    public function authorizedScopes(User $actor, Patients $patient): array
    {
        if (in_array($actor->user_type, [User::TYPE_ADMIN, User::TYPE_PRINCIPAL, User::TYPE_TEACHER], true)) {
            return [];
        }

        if ($this->isOwner($actor, $patient)
            || ($patient->requiresGuardianConsent() && $this->isVerifiedGuardian($actor, $patient))) {
            return array_keys(config('health_privacy.scopes', []));
        }

        return DataAuthorization::query()
            ->active()
            ->where('patient_id', $patient->id)
            ->where(function ($query) use ($actor): void {
                $query->where('recipient_user_id', $actor->id)
                    ->orWhereRaw('LOWER(recipient_email) = ?', [mb_strtolower($actor->email)]);
            })
            ->get(['scopes'])
            ->pluck('scopes')
            ->flatten()
            ->unique()
            ->values()
            ->all();
    }

    public function accessiblePatients(User $actor)
    {
        if (in_array($actor->user_type, [User::TYPE_ADMIN, User::TYPE_PRINCIPAL, User::TYPE_TEACHER], true)) {
            return Patients::query()->whereRaw('1 = 0');
        }

        $guardianControlEnabled = config('health_privacy.student_rights_transfer_mode') !== 'postsecondary';
        $minorCutoff = now()->subYears(18)->toDateString();

        return Patients::query()->where(function ($query) use ($actor, $guardianControlEnabled, $minorCutoff): void {
            $query->where('user_id', $actor->id);

            if ($guardianControlEnabled) {
                $query->orWhere(function ($guardianPatients) use ($actor, $minorCutoff): void {
                    $guardianPatients
                        ->where('registry_type', 'student')
                        ->whereHas('user', fn ($user) => $user->whereDate('dob', '>', $minorCutoff))
                        ->whereHas('guardians', function ($guardian) use ($actor): void {
                            $guardian->where('guardian_user_id', $actor->id)
                                ->where('is_active', true)
                                ->whereNotNull('verified_at')
                                ->whereNotNull('approved_at');
                        });
                });
            }

            $query->orWhereHas('dataAuthorizations', function ($authorization) use ($actor): void {
                $authorization->active()->where(function ($recipient) use ($actor): void {
                    $recipient->where('recipient_user_id', $actor->id)
                        ->orWhereRaw('LOWER(recipient_email) = ?', [mb_strtolower($actor->email)]);
                });
            });
        });
    }
}
