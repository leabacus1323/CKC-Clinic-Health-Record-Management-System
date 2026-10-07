<?php

namespace App\Http\Controllers;

use App\Models\ImmunizationRecords;
use App\Models\ImmunizationRequirement;
use App\Models\Patients;
use App\Models\User;
use App\Services\AccessAuditService;
use App\Services\ImmunizationComplianceService;
use App\Services\PatientAccessService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Illuminate\Validation\Rule;
use Illuminate\View\View;
use Symfony\Component\HttpFoundation\StreamedResponse;

class ImmunizationPortalController extends Controller
{
    public function __construct(
        private readonly PatientAccessService $access,
        private readonly ImmunizationComplianceService $compliance,
        private readonly AccessAuditService $audit,
    ) {
    }

    public function index(Request $request, Patients $patient): View
    {
        abort_unless($this->access->canViewScope($request->user(), $patient, 'immunizations'), 403);

        $patient->load(['user:id,name,dob', 'immunizations.requirement']);
        $requirements = ImmunizationRequirement::query()
            ->forRegistry($patient->isStudentRegistry() ? 'student' : 'non_student')
            ->orderBy('name')
            ->get();
        $compliance = $this->compliance->forPatient($patient);
        $canUpload = $this->access->canUploadImmunization($request->user(), $patient);

        $this->audit->record($request->user(), $patient, 'immunization_portal_viewed', 'authorized', ['immunizations'], null, [], $request);

        return view('immunizations.portal', compact('patient', 'requirements', 'compliance', 'canUpload'));
    }

    public function store(Request $request, Patients $patient): RedirectResponse
    {
        abort_unless($this->access->canUploadImmunization($request->user(), $patient), 403);

        $registry = $patient->isStudentRegistry() ? 'student' : 'non_student';
        $data = $request->validate([
            'immunization_requirement_id' => [
                'required',
                Rule::exists('immunization_requirements', 'id')->where(fn ($query) => $query
                    ->where('is_active', true)
                    ->whereIn('registry_type', ['all', $registry])),
            ],
            'administered_at' => ['required', 'date', 'before_or_equal:today'],
            'dose_number' => ['required', 'integer', 'min:1', 'max:20'],
            'administered_by' => ['nullable', 'string', 'max:255'],
            'expiration_date' => ['nullable', 'date', 'after:administered_at'],
            'notes' => ['nullable', 'string', 'max:1000'],
            'document' => ['required', 'file', 'mimes:pdf,jpg,jpeg,png', 'max:10240'],
        ]);

        $requirement = ImmunizationRequirement::findOrFail($data['immunization_requirement_id']);
        $file = $request->file('document');
        $extension = strtolower($file->getClientOriginalExtension());
        $storedName = Str::uuid().".{$extension}";
        $directory = "private/immunizations/{$patient->id}";
        $path = Storage::disk('local')->putFileAs($directory, $file, $storedName);
        abort_if($path === false, 500, 'The immunization document could not be stored.');

        try {
            $record = ImmunizationRecords::create([
            'immunization_requirement_id' => $requirement->id,
            'vaccine_name' => $requirement->name,
            'vaccine_type' => 'Uploaded record',
            'administered_at' => $data['administered_at'],
            'dose_number' => $data['dose_number'],
            'administered_by' => $data['administered_by'] ?? null,
            'dosage' => 'See uploaded record',
            'site_of_administration' => 'See uploaded record',
            'expiration_date' => $data['expiration_date'] ?? null,
            'notes' => $data['notes'] ?? null,
            'patient_id' => $patient->id,
            'verification_status' => 'pending',
            'source' => 'portal_upload',
            'document_path' => $path,
            'document_original_name' => Str::limit(basename($file->getClientOriginalName()), 240, ''),
            'document_mime' => $file->getMimeType(),
            'document_size' => $file->getSize(),
            'document_sha256' => hash_file('sha256', $file->getRealPath()),
                'uploaded_by_user_id' => $request->user()->id,
            ]);
        } catch (\Throwable $exception) {
            Storage::disk('local')->delete($path);
            throw $exception;
        }

        $this->audit->record($request->user(), $patient, 'immunization_document_uploaded', 'authorized', ['immunizations', 'documents'], 'Submit evidence for compliance review', [
            'immunization_record_id' => $record->id,
            'requirement_id' => $requirement->id,
            'document_sha256' => $record->document_sha256,
        ], $request);

        return back()->with('success', 'Immunization record uploaded securely and marked pending review.');
    }

    public function download(Request $request, Patients $patient, ImmunizationRecords $record): StreamedResponse
    {
        abort_unless($record->patient_id === $patient->id, 404);
        abort_unless($this->access->canViewScope($request->user(), $patient, 'documents'), 403);
        abort_unless($record->document_path && Storage::disk('local')->exists($record->document_path), 404);

        $this->audit->record($request->user(), $patient, 'immunization_document_downloaded', 'authorized', ['immunizations', 'documents'], null, [
            'immunization_record_id' => $record->id,
            'document_sha256' => $record->document_sha256,
        ], $request);

        return Storage::disk('local')->download($record->document_path, $record->document_original_name);
    }

    public function reviewIndex(Request $request): View
    {
        abort_unless(in_array($request->user()->user_type, [User::TYPE_DOCTOR, User::TYPE_STAFF], true), 403);

        $records = ImmunizationRecords::query()
            ->with(['patient.user:id,name,email', 'requirement'])
            ->where('verification_status', 'pending')
            ->latest('id')
            ->paginate(25);

        return view('immunizations.review', compact('records'));
    }

    public function review(Request $request, ImmunizationRecords $record): RedirectResponse
    {
        abort_unless(in_array($request->user()->user_type, [User::TYPE_DOCTOR, User::TYPE_STAFF], true), 403);
        abort_unless($record->verification_status === 'pending', 409, 'This submission has already been reviewed.');

        $data = $request->validate([
            'decision' => ['required', Rule::in(['verified', 'rejected'])],
            'rejection_reason' => ['nullable', 'required_if:decision,rejected', 'string', 'max:1000'],
        ]);

        $record->update([
            'verification_status' => $data['decision'],
            'verified_by_user_id' => $data['decision'] === 'verified' ? $request->user()->id : null,
            'verified_at' => $data['decision'] === 'verified' ? now() : null,
            'rejected_at' => $data['decision'] === 'rejected' ? now() : null,
            'rejection_reason' => $data['decision'] === 'rejected' ? $data['rejection_reason'] : null,
        ]);

        $this->audit->record($request->user(), $record->patient, 'immunization_submission_'.$data['decision'], 'clinical', ['immunizations', 'documents'], 'Compliance evidence review', [
            'immunization_record_id' => $record->id,
            'decision' => $data['decision'],
        ], $request);

        return back()->with('success', 'Submission marked '.$data['decision'].'.');
    }
}
