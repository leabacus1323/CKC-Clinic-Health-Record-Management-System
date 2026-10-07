<?php

namespace App\Http\Controllers;

use App\Models\AccessAuditLog;
use App\Models\BreakGlassEvent;
use App\Models\Patients;
use App\Models\User;
use App\Services\AccessAuditService;
use App\Services\AuditIntegrityService;
use App\Services\BreakGlassService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class BreakGlassController extends Controller
{
    public function __construct(
        private readonly BreakGlassService $breakGlass,
        private readonly AccessAuditService $audit,
        private readonly AuditIntegrityService $integrity,
    ) {
    }

    public function index(Request $request): View
    {
        abort_unless($request->user()->user_type === User::TYPE_PRINCIPAL, 403);

        $search = trim((string) $request->query('search'));
        $patients = Patients::query()
            ->with('user:id,name,email')
            ->when($search !== '', function ($query) use ($search): void {
                $query->whereHas('user', function ($user) use ($search): void {
                    $user->where('name', 'like', "%{$search}%")
                        ->orWhere('email', 'like', "%{$search}%");
                })->orWhere('school_id', 'like', "%{$search}%");
            })
            ->orderByDesc('id')
            ->paginate(25)
            ->withQueryString();

        $recentEvents = BreakGlassEvent::query()
            ->with(['actor:id,name,email', 'patient.user:id,name'])
            ->latest('id')
            ->limit(20)
            ->get();

        $this->audit->record(
            $request->user(),
            null,
            'break_glass_console_viewed',
            'emergency_administration',
            [],
            'Locate a record during an emergency',
            ['search_used' => $search !== ''],
            $request,
        );

        return view('break-glass.index', compact('patients', 'recentEvents', 'search'));
    }

    public function create(Request $request, Patients $patient): View
    {
        abort_unless($request->user()->user_type === User::TYPE_PRINCIPAL, 403);
        $patient->load('user:id,name,email');

        $this->audit->record(
            $request->user(),
            $patient,
            'break_glass_activation_form_viewed',
            'emergency_administration',
            ['identity'],
            'Evaluate whether emergency access is necessary',
            [],
            $request,
        );

        return view('break-glass.create', compact('patient'));
    }

    public function store(Request $request, Patients $patient): RedirectResponse
    {
        $data = $request->validate([
            'threat_description' => ['required', 'string', 'min:20', 'max:2000'],
            'justification' => ['required', 'string', 'min:20', 'max:2000'],
            'recipient_party' => ['nullable', 'string', 'max:255'],
            'current_password' => ['required', 'current_password'],
            'confirmation' => ['accepted'],
        ]);

        $this->breakGlass->activate($request->user(), $patient, $data, $request);

        return redirect()->route('break-glass.show', $patient)
            ->with('warning', 'Emergency access is active for a limited time. Every view is audited.');
    }

    public function show(Request $request, Patients $patient): View
    {
        abort_unless($request->user()->user_type === User::TYPE_PRINCIPAL, 403);
        $event = $this->breakGlass->activeEvent($request->user(), $patient, $request);
        abort_unless($event, 403, 'No active emergency access grant exists for this record.');

        $patient->load('user:id,name,dob,contact_no');
        $scopes = config('health_privacy.break_glass_scopes', []);
        $this->audit->record($request->user(), $patient, 'break_glass_record_viewed', 'break_glass', $scopes, 'Emergency minimum-necessary view', [
            'event_public_id' => $event->public_id,
        ], $request);

        return view('break-glass.show', compact('patient', 'event'));
    }

    public function audit(Request $request): View
    {
        abort_unless($request->user()->user_type === User::TYPE_PRINCIPAL, 403);

        $logs = AccessAuditLog::query()
            ->with(['actor:id,name,email', 'patient.user:id,name'])
            ->latest('id')
            ->paginate(50);

        $integrity = $this->integrity->verify();

        $this->audit->record(
            $request->user(),
            null,
            'security_audit_log_viewed',
            'security_oversight',
            [],
            'Review emergency and access activity',
            ['integrity_valid' => collect($integrity)->every(fn (array $result): bool => $result['valid'])],
            $request,
        );

        return view('break-glass.audit', compact('logs', 'integrity'));
    }
}
