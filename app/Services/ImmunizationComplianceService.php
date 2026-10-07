<?php

namespace App\Services;

use App\Models\ImmunizationRequirement;
use App\Models\Patients;
use Carbon\Carbon;
use Illuminate\Support\Collection;

class ImmunizationComplianceService
{
    public function forPatient(Patients $patient): Collection
    {
        $patient->loadMissing(['user:id,dob', 'immunizations']);
        $registry = $patient->isStudentRegistry() ? 'student' : 'non_student';
        $ageMonths = $patient->user?->dob ? Carbon::parse($patient->user->dob)->diffInMonths(now()) : null;

        return ImmunizationRequirement::query()
            ->forRegistry($registry)
            ->orderBy('name')
            ->get()
            ->map(function (ImmunizationRequirement $requirement) use ($patient, $ageMonths): array {
                $records = $patient->immunizations
                    ->where('immunization_requirement_id', $requirement->id);
                $verified = $records->where('verification_status', 'verified');
                $pending = $records->where('verification_status', 'pending')->count();
                $valid = $verified->filter(
                    fn ($record) => $record->expiration_date === null || $record->expiration_date->isFuture()
                );
                $latestVerified = $valid->sortByDesc('administered_at')->first();
                $notYetDue = $requirement->due_age_months !== null
                    && $ageMonths !== null
                    && $ageMonths < $requirement->due_age_months;

                $nextDueDate = null;
                $status = 'missing';

                if ($notYetDue) {
                    $status = 'not_due';
                } elseif ($valid->count() >= $requirement->minimum_doses) {
                    $status = 'compliant';

                    if ($requirement->booster_interval_months && $latestVerified?->administered_at) {
                        $nextDueDate = $latestVerified->administered_at
                            ->copy()
                            ->addMonths($requirement->booster_interval_months);

                        if ($nextDueDate->isPast()) {
                            $status = 'booster_due';
                        } elseif ($nextDueDate->lte(now()->addDays(30))) {
                            $status = 'due_soon';
                        }
                    } elseif ($valid->contains(fn ($record) => $record->expiration_date?->between(now(), now()->addDays(30)) ?? false)) {
                        $status = 'due_soon';
                    }
                } elseif ($pending > 0) {
                    $status = 'pending_review';
                } elseif ($verified->isNotEmpty()) {
                    $status = 'expired_or_incomplete';
                }

                if (! in_array($status, ['compliant', 'not_due', 'pending_review'], true) && $requirement->required_by_date) {
                    $nextDueDate = $requirement->required_by_date->copy();
                    if ($requirement->required_by_date->isPast()) {
                        $status = 'overdue';
                    } elseif ($requirement->required_by_date->lte(now()->addDays(30))) {
                        $status = 'due_soon';
                    }
                }

                return [
                    'requirement' => $requirement,
                    'status' => $status,
                    'verified_doses' => $valid->count(),
                    'pending_submissions' => $pending,
                    'required_doses' => $requirement->minimum_doses,
                    'latest_record' => $records->sortByDesc('administered_at')->first(),
                    'next_due_date' => $nextDueDate,
                ];
            });
    }
}
