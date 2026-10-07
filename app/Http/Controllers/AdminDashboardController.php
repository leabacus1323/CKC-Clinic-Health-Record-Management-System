<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\DB;
use Illuminate\View\View;

class AdminDashboardController extends Controller
{
    public function index(): View
    {
        $ttl = max(1, (int) config('performance.dashboard_cache_ttl', 60));

        $summary = Cache::remember('dashboard.system-administration.summary', $ttl, function (): array {
            $counts = DB::table('users')
                ->select('user_type', DB::raw('COUNT(*) AS aggregate'))
                ->groupBy('user_type')
                ->pluck('aggregate', 'user_type');

            return [
                'total_users' => (int) $counts->sum(),
                'administrators' => (int) ($counts[User::TYPE_ADMIN] ?? 0),
                'nurses' => (int) ($counts[User::TYPE_STAFF] ?? 0),
                'doctors' => (int) ($counts[User::TYPE_DOCTOR] ?? 0),
                'teachers' => (int) ($counts[User::TYPE_TEACHER] ?? 0),
                'guardians' => (int) ($counts[User::TYPE_GUARDIAN] ?? 0),
            ];
        });

        return view('admin.dashboard', [
            'summary' => $summary,
            'system' => [
                'environment' => app()->environment(),
                'database' => config('database.default'),
                'cache' => config('cache.default'),
                'session' => config('session.driver'),
                'queue' => config('queue.default'),
                'idleLock' => config('medical_security.idle_timeout_seconds'),
                'auditDriver' => config('medical_security.audit.driver'),
                'auditFailClosed' => config('medical_security.audit.fail_closed'),
                'auditExternalRequired' => config('medical_security.audit.require_external_boundary'),
                'auditSeparationConfirmed' => config('medical_security.audit.separation_confirmed'),
            ],
        ]);
    }
}
