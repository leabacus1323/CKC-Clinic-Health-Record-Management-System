<?php

namespace App\Http\Controllers;

use App\Models\Announcement;
use App\Models\Appointment;
use App\Models\CertificateRequest;
use App\Models\ImmunizationRecords;
use App\Models\PatientGuardian;
use App\Models\Patients;
use App\Models\User;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Route;

class NotificationsController extends Controller
{
    /**
     * Return role-aware appointment, request, and announcement notifications.
     */
    public function getNotifications(): JsonResponse
    {
        /** @var User $user */
        $user = Auth::user();

        $notifications = [
            'appointments' => [],
            'requests' => [],
            'announcements' => [],
            'total' => 0,
            'generated_at' => now()->toIso8601String(),
        ];

        if (in_array($user->user_type, [User::TYPE_STUDENT, User::TYPE_FACULTY], true)) {
            $this->patientNotifications($user, $notifications);
        } elseif (in_array($user->user_type, [User::TYPE_STAFF, User::TYPE_DOCTOR], true)) {
            $this->clinicalNotifications($user, $notifications);
        } elseif ($user->user_type === User::TYPE_ADMIN) {
            $this->administratorNotifications($notifications);
        } elseif ($user->user_type === User::TYPE_GUARDIAN) {
            $this->guardianNotifications($user, $notifications);
        }

        $announcementUrl = $user->user_type === User::TYPE_STAFF && Route::has('staff.announcements')
            ? route('staff.announcements')
            : null;

        $notifications['announcements'] = Announcement::query()
            ->where('active', true)
            ->latest()
            ->limit(5)
            ->get(['id', 'title', 'message', 'created_at'])
            ->map(static fn (Announcement $announcement): array => [
                'id' => 'announcement-'.$announcement->id,
                'title' => $announcement->title,
                'message' => str($announcement->message)->stripTags()->limit(110)->toString(),
                'created_at' => $announcement->created_at?->toIso8601String(),
                'type' => 'announcement',
                'url' => $announcementUrl,
                'count' => 1,
            ])
            ->values()
            ->all();

        $notifications['total'] = collect(['appointments', 'requests', 'announcements'])
            ->flatMap(fn (string $key) => $notifications[$key])
            ->sum(fn (array $item) => max(1, (int) ($item['count'] ?? 1)));

        return response()->json($notifications)
            ->header('Cache-Control', 'no-store, private');
    }

    private function patientNotifications(User $user, array &$notifications): void
    {
        $patient = Patients::where('user_id', $user->id)->first();
        if (! $patient) {
            return;
        }

        $notifications['appointments'] = Appointment::query()
            ->where('patient_id', $patient->id)
            ->whereDate('date', '>=', today())
            ->whereIn('status', [Appointment::STATUS_PENDING, Appointment::STATUS_CONFIRMED])
            ->with('doctor.user:id,name')
            ->orderBy('date')
            ->orderBy('time')
            ->limit(5)
            ->get()
            ->map(static fn (Appointment $appointment): array => [
                'id' => 'appointment-'.$appointment->id,
                'title' => 'Appointment with '.($appointment->doctor?->user?->name ?? 'clinic provider'),
                'message' => trim($appointment->date.' at '.$appointment->time),
                'status' => strtolower((string) $appointment->status),
                'type' => 'appointment',
                'url' => route('patients.appointments'),
                'count' => 1,
            ])
            ->values()
            ->all();

        $notifications['requests'] = CertificateRequest::query()
            ->where('patient_id', $patient->id)
            ->whereIn('status', [CertificateRequest::STATUS_PENDING, CertificateRequest::STATUS_APPROVED])
            ->with('certificateType:id,name')
            ->latest()
            ->limit(5)
            ->get()
            ->map(static fn (CertificateRequest $certificate): array => [
                'id' => 'certificate-'.$certificate->id,
                'title' => $certificate->certificateType?->name ?? 'Medical certificate request',
                'message' => 'Request status: '.ucfirst($certificate->status),
                'status' => $certificate->status,
                'type' => 'request',
                'url' => route('patients.certificates.show', $certificate->id),
                'count' => 1,
            ])
            ->values()
            ->all();
    }

    private function clinicalNotifications(User $user, array &$notifications): void
    {
        $appointmentRoute = $user->user_type === User::TYPE_DOCTOR
            ? route('doctor.appointments')
            : route('staff.appointments');
        $requestRoute = $user->user_type === User::TYPE_DOCTOR
            ? 'doctor.certificate-requests.show'
            : 'staff.certificate-requests.show';

        $appointmentQuery = Appointment::query()
            ->whereDate('date', '>=', today())
            ->where('status', Appointment::STATUS_PENDING)
            ->with('patient.user:id,name')
            ->orderBy('date')
            ->orderBy('time');

        if ($user->user_type === User::TYPE_DOCTOR && $user->doctor) {
            $appointmentQuery->where('doc_id', $user->doctor->id);
        }

        $notifications['appointments'] = $appointmentQuery
            ->limit(5)
            ->get()
            ->map(static fn (Appointment $appointment): array => [
                'id' => 'appointment-'.$appointment->id,
                'title' => $appointment->patient?->user?->name ?? 'Patient appointment',
                'message' => trim($appointment->date.' at '.$appointment->time),
                'status' => 'pending',
                'type' => 'appointment',
                'url' => $appointmentRoute,
                'count' => 1,
            ])
            ->values()
            ->all();

        $certificateQuery = CertificateRequest::query()
            ->where('status', CertificateRequest::STATUS_PENDING)
            ->with(['patient.user:id,name', 'certificateType:id,name'])
            ->latest();

        if ($user->user_type === User::TYPE_DOCTOR && $user->doctor) {
            $certificateQuery->where(function ($query) use ($user): void {
                $query->whereNull('doctor_id')->orWhere('doctor_id', $user->doctor->id);
            });
        }

        $requests = $certificateQuery
            ->limit(5)
            ->get()
            ->map(static fn (CertificateRequest $certificate): array => [
                'id' => 'certificate-'.$certificate->id,
                'title' => $certificate->certificateType?->name ?? 'Certificate request',
                'message' => 'Requested by '.($certificate->patient?->user?->name ?? 'patient'),
                'status' => 'pending',
                'type' => 'request',
                'url' => route($requestRoute, $certificate->id),
                'count' => 1,
            ]);

        $pendingImmunizations = ImmunizationRecords::query()
            ->where('verification_status', 'pending')
            ->count();

        if ($pendingImmunizations > 0) {
            $requests->push([
                'id' => 'immunization-review',
                'title' => 'Immunization records awaiting review',
                'message' => $pendingImmunizations.' submission(s) need verification',
                'status' => 'pending',
                'type' => 'request',
                'url' => route('immunizations.review.index'),
                'count' => $pendingImmunizations,
            ]);
        }

        $notifications['requests'] = $requests->values()->all();
    }

    private function administratorNotifications(array &$notifications): void
    {
        $pendingAppointments = Appointment::where('status', Appointment::STATUS_PENDING)->count();
        if ($pendingAppointments > 0) {
            $notifications['appointments'][] = [
                'id' => 'admin-pending-appointments',
                'title' => 'Pending clinic appointments',
                'message' => $pendingAppointments.' appointment(s) require clinic action',
                'status' => 'pending',
                'type' => 'appointment',
                'url' => route('admin.analytics.index'),
                'count' => $pendingAppointments,
            ];
        }

        $pendingGuardians = PatientGuardian::query()
            ->where('is_active', true)
            ->whereNull('approved_at')
            ->count();
        if ($pendingGuardians > 0) {
            $notifications['requests'][] = [
                'id' => 'guardian-approvals',
                'title' => 'Guardian verification requests',
                'message' => $pendingGuardians.' relationship(s) await approval',
                'status' => 'pending',
                'type' => 'request',
                'url' => route('guardian-approvals.index'),
                'count' => $pendingGuardians,
            ];
        }

        $pendingCertificates = CertificateRequest::where('status', CertificateRequest::STATUS_PENDING)->count();
        if ($pendingCertificates > 0) {
            $notifications['requests'][] = [
                'id' => 'admin-certificate-workload',
                'title' => 'Pending certificate workload',
                'message' => $pendingCertificates.' request(s) are awaiting clinical review',
                'status' => 'pending',
                'type' => 'request',
                'url' => route('admin.analytics.index'),
                'count' => $pendingCertificates,
            ];
        }
    }

    private function guardianNotifications(User $user, array &$notifications): void
    {
        $pendingLinks = PatientGuardian::query()
            ->where('guardian_user_id', $user->id)
            ->where('is_active', true)
            ->whereNull('approved_at')
            ->count();

        if ($pendingLinks > 0) {
            $notifications['requests'][] = [
                'id' => 'guardian-link-status',
                'title' => 'Guardian access awaiting verification',
                'message' => $pendingLinks.' access request(s) are still pending',
                'status' => 'pending',
                'type' => 'request',
                'url' => route('privacy-center.index'),
                'count' => $pendingLinks,
            ];
        }
    }
}

