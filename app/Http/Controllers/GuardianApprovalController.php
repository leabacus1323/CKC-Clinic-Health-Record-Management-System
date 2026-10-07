<?php

namespace App\Http\Controllers;

use App\Models\PatientGuardian;
use App\Models\User;
use App\Services\AccessAuditService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;
use Illuminate\View\View;

class GuardianApprovalController extends Controller
{
    public function __construct(private readonly AccessAuditService $audit)
    {
    }

    public function index(Request $request): View
    {
        abort_unless($request->user()->user_type === User::TYPE_ADMIN, 403);

        $status = in_array($request->query('status'), ['pending', 'approved', 'rejected'], true)
            ? (string) $request->query('status')
            : 'pending';

        $links = PatientGuardian::query()
            ->with([
                'patient.user:id,name,email,dob',
                'guardianUser:id,name,email,dob',
                'approvedBy:id,name,email',
            ])
            ->whereNotNull('verified_at')
            ->when($status === 'pending', fn ($query) => $query->whereNull('approved_at')->where('is_active', true))
            ->when($status === 'approved', fn ($query) => $query->whereNotNull('approved_at')->where('is_active', true))
            ->when($status === 'rejected', fn ($query) => $query->where('is_active', false)->whereNull('approved_at'))
            ->latest('id')
            ->paginate(25)
            ->withQueryString();

        return view('privacy-center.guardian-approvals', compact('links', 'status'));
    }

    public function update(Request $request, PatientGuardian $guardianLink): RedirectResponse
    {
        abort_unless($request->user()->user_type === User::TYPE_ADMIN, 403);
        abort_if($guardianLink->verified_at === null, 422, 'The guardian must accept the invitation before approval.');

        $data = $request->validate([
            'decision' => ['required', Rule::in(['approve', 'reject'])],
            'approval_notes' => ['required', 'string', 'min:10', 'max:2000'],
        ]);

        if ($data['decision'] === 'approve') {
            $guardianLink->update([
                'approved_at' => now(),
                'approved_by_user_id' => $request->user()->id,
                'approval_notes' => $data['approval_notes'],
                'is_active' => true,
            ]);
            $action = 'guardian_relationship_approved';
            $message = 'Guardian relationship approved.';
        } else {
            $guardianLink->update([
                'approved_at' => null,
                'approved_by_user_id' => $request->user()->id,
                'approval_notes' => $data['approval_notes'],
                'is_active' => false,
                'can_manage_consents' => false,
            ]);
            $action = 'guardian_relationship_rejected';
            $message = 'Guardian relationship rejected and access disabled.';
        }

        $this->audit->record(
            $request->user(),
            $guardianLink->patient,
            $action,
            'consent',
            ['identity'],
            $data['approval_notes'],
            ['guardian_link_id' => $guardianLink->id, 'guardian_user_id' => $guardianLink->guardian_user_id],
            $request,
        );

        return back()->with('success', $message);
    }
}
