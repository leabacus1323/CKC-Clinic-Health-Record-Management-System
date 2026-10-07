<?php

namespace App\Providers;

use App\Models\Announcement;
use App\Models\Appointment;
use App\Models\CertificateRequest;
use App\Models\User;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\View;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    public function register(): void
    {
        //
    }

    public function boot(): void
    {
        // Bind database-backed notification data only to the navigation view.
        // The previous wildcard composer queried announcements for every partial view.
        View::composer('layouts.navigation', function ($view): void {
            $announcementTtl = max(1, config('performance.announcement_cache_ttl', 300));
            $navigationTtl = max(1, config('performance.navigation_cache_ttl', 30));

            $siteAnnouncements = Cache::remember(
                'navigation.active_announcements.latest',
                $announcementTtl,
                fn () => Announcement::query()
                    ->with('creator:id,name')
                    ->where('active', true)
                    ->latest('created_at')
                    ->limit(3)
                    ->get(['id', 'title', 'message', 'created_by', 'created_at'])
            );

            $announcementCount = Cache::remember(
                'navigation.active_announcements.count',
                $announcementTtl,
                fn () => Announcement::query()->where('active', true)->count()
            );

            $counts = $this->emptyNotificationCounts();
            $user = Auth::user();

            if ($user) {
                $counts = Cache::remember(
                    "navigation.notifications.{$user->id}.{$user->user_type}",
                    $navigationTtl,
                    fn () => $this->navigationNotificationCounts($user)
                );
            }

            $view->with(array_merge($counts, [
                'siteAnnouncements' => $siteAnnouncements,
                'siteAnnouncementsCount' => $announcementCount,
                'announcementCount' => $announcementCount,
                'totalNotificationCount' => $announcementCount
                    + $counts['patientNotificationCount']
                    + $counts['pendingAppointmentCount'],
            ]));
        });
    }

    private function emptyNotificationCounts(): array
    {
        return [
            'confirmedAppointmentCount' => 0,
            'cancelledAppointmentCount' => 0,
            'approvedCertificateCount' => 0,
            'rejectedCertificateCount' => 0,
            'issuedCertificateCount' => 0,
            'patientNotificationCount' => 0,
            'pendingAppointmentCount' => 0,
        ];
    }

    private function navigationNotificationCounts(User $user): array
    {
        $counts = $this->emptyNotificationCounts();

        if (in_array($user->user_type, [User::TYPE_STUDENT, User::TYPE_FACULTY], true)) {
            $patientId = DB::table('patients')->where('user_id', $user->id)->value('id');

            if ($patientId) {
                $appointmentCounts = DB::table('appointments')
                    ->where('patient_id', $patientId)
                    ->whereIn('status', [Appointment::STATUS_CONFIRMED, Appointment::STATUS_CANCELLED])
                    ->select('status', DB::raw('COUNT(*) AS aggregate'))
                    ->groupBy('status')
                    ->pluck('aggregate', 'status');

                $certificateCounts = DB::table('certificate_requests')
                    ->where('patient_id', $patientId)
                    ->whereIn('status', [
                        CertificateRequest::STATUS_APPROVED,
                        CertificateRequest::STATUS_REJECTED,
                        CertificateRequest::STATUS_ISSUED,
                    ])
                    ->select('status', DB::raw('COUNT(*) AS aggregate'))
                    ->groupBy('status')
                    ->pluck('aggregate', 'status');

                $counts['confirmedAppointmentCount'] = (int) ($appointmentCounts[Appointment::STATUS_CONFIRMED] ?? 0);
                $counts['cancelledAppointmentCount'] = (int) ($appointmentCounts[Appointment::STATUS_CANCELLED] ?? 0);
                $counts['approvedCertificateCount'] = (int) ($certificateCounts[CertificateRequest::STATUS_APPROVED] ?? 0);
                $counts['rejectedCertificateCount'] = (int) ($certificateCounts[CertificateRequest::STATUS_REJECTED] ?? 0);
                $counts['issuedCertificateCount'] = (int) ($certificateCounts[CertificateRequest::STATUS_ISSUED] ?? 0);
                $counts['patientNotificationCount'] = $counts['confirmedAppointmentCount']
                    + $counts['cancelledAppointmentCount']
                    + $counts['approvedCertificateCount']
                    + $counts['rejectedCertificateCount']
                    + $counts['issuedCertificateCount'];
            }
        } elseif ($user->user_type === User::TYPE_STAFF) {
            $counts['pendingAppointmentCount'] = Appointment::query()
                ->where('status', Appointment::STATUS_PENDING)
                ->count();
        } elseif ($user->user_type === User::TYPE_DOCTOR) {
            $doctorId = DB::table('doctors')->where('user_id', $user->id)->value('id');

            if ($doctorId) {
                $counts['pendingAppointmentCount'] = Appointment::query()
                    ->where('doc_id', $doctorId)
                    ->where('status', Appointment::STATUS_PENDING)
                    ->count();
            }
        }

        return $counts;
    }
}
