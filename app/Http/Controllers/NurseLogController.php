<?php

namespace App\Http\Controllers;

use App\Models\Medicine;
use App\Models\NurseBed;
use App\Models\NurseLog;
use App\Models\NurseLogIntervention;
use App\Models\Patients;
use App\Models\PrescriptionRecord;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\ValidationException;
use Illuminate\View\View;

class NurseLogController extends Controller
{
    public function index(Request $request): View
    {
        $query = NurseLog::query()
            ->with([
                'patient.user',
                'staff:id,name',
                'medicine:id,name,quantity,unit',
                'prescription:id,status,date_prescribed',
                'bed.activeRestLog.patient.user',
                'interventions.medicine:id,name,quantity,unit',
                'interventions.prescription:id,status,date_prescribed',
                'interventions.bed',
            ])
            ->latest('time_in');

        if ($request->filled('search')) {
            $search = (string) $request->search;
            $query->where(function ($nested) use ($search): void {
                $nested->whereHas('patient.user', function ($q) use ($search): void {
                    $q->where('name', 'like', "%{$search}%")
                        ->orWhere('email', 'like', "%{$search}%");
                })->orWhereHas('patient', function ($q) use ($search): void {
                    $q->where('school_id', 'like', "%{$search}%");
                });
            });
        }

        if ($request->filled('intervention_type')) {
            $query->where(function ($nested) use ($request): void {
                $nested->where('intervention_type', $request->intervention_type)
                    ->orWhereHas('interventions', function ($q) use ($request): void {
                        $q->where('intervention_type', $request->intervention_type);
                    });
            });
        }

        if ($request->filled('date')) {
            $query->whereDate('time_in', $request->date);
        }

        $logs = $query->paginate(15)->withQueryString();

        $summary = [
            'today' => NurseLog::whereDate('time_in', today())->count(),
            'open' => NurseLog::whereNull('time_out')->count(),
            'medicine' => $this->countTodayByIntervention(NurseLog::INTERVENTION_PRESCRIBED_MEDICINE),
            'mgh' => $this->countTodayByIntervention(NurseLog::INTERVENTION_MGH),
        ];

        $bedSummary = $this->bedSummary();
        $beds = NurseBed::query()
            ->with('activeRestLog.patient.user')
            ->where('is_active', true)
            ->orderBy('name')
            ->get();

        return view('staff.nurse-logs.index', compact('logs', 'summary', 'bedSummary', 'beds'));
    }

    public function create(): View
    {
        $patients = Patients::query()
            ->with('user:id,name,email')
            ->whereHas('user')
            ->orderByDesc('created_at')
            ->get();

        $medicines = Medicine::query()
            ->where('quantity', '>', 0)
            ->orderBy('name')
            ->get(['id', 'name', 'generic_name', 'dosage', 'unit', 'quantity']);

        $beds = NurseBed::query()
            ->with('activeRestLog.patient.user')
            ->where('is_active', true)
            ->orderBy('name')
            ->get();

        $bedSummary = $this->bedSummary();

        return view('staff.nurse-logs.create', compact('patients', 'medicines', 'beds', 'bedSummary'));
    }

    public function store(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'patient_id' => ['required', 'exists:patients,id'],
            'time_in' => ['required', 'date'],
            'time_out' => ['nullable', 'date', 'after_or_equal:time_in'],
            'complaint' => ['required', 'string', 'max:5000'],
            'interventions' => ['required', 'array', 'min:1'],
            'interventions.*' => ['in:prescribed_medicine,mgh,rest,first_aid,other'],
            'intervention_notes' => ['nullable', 'string', 'max:5000'],
            'medicine_id' => ['nullable', 'exists:medicine,id'],
            'medicine_quantity' => ['nullable', 'integer', 'min:1'],
            'dosage' => ['nullable', 'string', 'max:255'],
            'frequency' => ['nullable', 'string', 'max:255'],
            'duration' => ['nullable', 'string', 'max:255'],
            'prescription_instruction' => ['nullable', 'string', 'max:5000'],
            'mgh_status' => ['nullable', 'string', 'max:255'],
            'rest_minutes' => ['nullable', 'integer', 'min:1', 'max:480'],
            'nurse_bed_id' => ['nullable', 'exists:nurse_beds,id'],
            'first_aid_details' => ['nullable', 'string', 'max:5000'],
        ]);

        $selectedInterventions = collect($validated['interventions'])
            ->unique()
            ->values()
            ->all();

        $this->validateSelectedInterventionDetails($selectedInterventions, $validated);

        DB::transaction(function () use ($validated, $selectedInterventions): void {
            $medicine = null;
            $prescription = null;
            $medicineId = null;
            $quantity = null;
            $bedId = null;

            if (in_array(NurseLog::INTERVENTION_REST, $selectedInterventions, true)) {
                $bed = NurseBed::whereKey($validated['nurse_bed_id'] ?? null)
                    ->where('is_active', true)
                    ->lockForUpdate()
                    ->firstOrFail();

                $isOccupied = NurseLog::query()
                    ->where('nurse_bed_id', $bed->id)
                    ->whereNull('time_out')
                    ->where(function ($query): void {
                        $query->where('intervention_type', NurseLog::INTERVENTION_REST)
                            ->orWhereHas('interventions', function ($q): void {
                                $q->where('intervention_type', NurseLog::INTERVENTION_REST);
                            });
                    })
                    ->exists();

                if ($isOccupied) {
                    throw ValidationException::withMessages([
                        'nurse_bed_id' => 'The selected bed is already occupied. Please choose an available bed.',
                    ]);
                }

                $bedId = $bed->id;
            }

            if (in_array(NurseLog::INTERVENTION_PRESCRIBED_MEDICINE, $selectedInterventions, true)) {
                $medicineId = $validated['medicine_id'] ?? null;
                $quantity = (int) ($validated['medicine_quantity'] ?? 0);
                $medicine = Medicine::whereKey($medicineId)->lockForUpdate()->firstOrFail();

                if ((int) $medicine->quantity < $quantity) {
                    throw ValidationException::withMessages([
                        'medicine_quantity' => 'The selected medicine does not have enough available stock.',
                    ]);
                }

                $prescription = PrescriptionRecord::create([
                    'patient_id' => $validated['patient_id'],
                    'medicine_id' => $medicine->id,
                    'dosage' => $validated['dosage'],
                    'frequency' => $validated['frequency'] ?? null,
                    'duration' => $validated['duration'] ?? null,
                    'instruction' => $validated['prescription_instruction']
                        ?? $validated['intervention_notes']
                        ?? 'Issued by clinic staff through nurse log.',
                    'doctor_id' => null,
                    'prescribed_by' => Auth::id(),
                    'status' => 'active',
                    'date_prescribed' => now()->toDateString(),
                ]);

                $medicine->decrement('quantity', $quantity);
            }

            $primaryIntervention = $this->primaryIntervention($selectedInterventions);

            $nurseLog = NurseLog::create([
                'patient_id' => $validated['patient_id'],
                'staff_user_id' => Auth::id(),
                'time_in' => $validated['time_in'],
                'time_out' => $validated['time_out'] ?? null,
                'complaint' => $validated['complaint'],
                'intervention_type' => $primaryIntervention,
                'intervention_notes' => $validated['intervention_notes'] ?? null,
                'medicine_id' => $medicineId,
                'medicine_quantity' => $quantity,
                'prescription_record_id' => $prescription?->id,
                'mgh_status' => in_array(NurseLog::INTERVENTION_MGH, $selectedInterventions, true)
                    ? ($validated['mgh_status'] ?? null)
                    : null,
                'rest_minutes' => in_array(NurseLog::INTERVENTION_REST, $selectedInterventions, true)
                    ? ($validated['rest_minutes'] ?? null)
                    : null,
                'nurse_bed_id' => $bedId,
                'first_aid_details' => in_array(NurseLog::INTERVENTION_FIRST_AID, $selectedInterventions, true)
                    ? ($validated['first_aid_details'] ?? null)
                    : null,
            ]);

            foreach ($selectedInterventions as $interventionType) {
                NurseLogIntervention::create([
                    'nurse_log_id' => $nurseLog->id,
                    'intervention_type' => $interventionType,
                    'notes' => $validated['intervention_notes'] ?? null,
                    'medicine_id' => $interventionType === NurseLog::INTERVENTION_PRESCRIBED_MEDICINE ? $medicineId : null,
                    'medicine_quantity' => $interventionType === NurseLog::INTERVENTION_PRESCRIBED_MEDICINE ? $quantity : null,
                    'prescription_record_id' => $interventionType === NurseLog::INTERVENTION_PRESCRIBED_MEDICINE ? $prescription?->id : null,
                    'dosage' => $interventionType === NurseLog::INTERVENTION_PRESCRIBED_MEDICINE ? ($validated['dosage'] ?? null) : null,
                    'frequency' => $interventionType === NurseLog::INTERVENTION_PRESCRIBED_MEDICINE ? ($validated['frequency'] ?? null) : null,
                    'duration' => $interventionType === NurseLog::INTERVENTION_PRESCRIBED_MEDICINE ? ($validated['duration'] ?? null) : null,
                    'prescription_instruction' => $interventionType === NurseLog::INTERVENTION_PRESCRIBED_MEDICINE ? ($validated['prescription_instruction'] ?? null) : null,
                    'mgh_status' => $interventionType === NurseLog::INTERVENTION_MGH ? ($validated['mgh_status'] ?? null) : null,
                    'rest_minutes' => $interventionType === NurseLog::INTERVENTION_REST ? ($validated['rest_minutes'] ?? null) : null,
                    'nurse_bed_id' => $interventionType === NurseLog::INTERVENTION_REST ? $bedId : null,
                    'first_aid_details' => $interventionType === NurseLog::INTERVENTION_FIRST_AID ? ($validated['first_aid_details'] ?? null) : null,
                ]);
            }
        });

        return redirect()->route('staff.nurse-logs.index')
            ->with('success', 'Nurse log saved successfully. All selected interventions were recorded. Medicine interventions were added to the prescriptions dashboard when applicable.');
    }

    public function checkout(NurseLog $nurseLog): RedirectResponse
    {
        if ($nurseLog->time_out === null) {
            $nurseLog->update(['time_out' => now()]);
        }

        return redirect()->back()->with('success', 'Patient time-out recorded successfully. Bed availability was updated when applicable.');
    }

    private function bedSummary(): array
    {
        $beds = NurseBed::query()
            ->with('activeRestLog')
            ->where('is_active', true)
            ->get();

        $occupied = $beds->filter(fn (NurseBed $bed): bool => $bed->activeRestLog !== null)->count();

        return [
            'total' => $beds->count(),
            'occupied' => $occupied,
            'available' => max($beds->count() - $occupied, 0),
        ];
    }


    private function validateSelectedInterventionDetails(array $interventions, array $validated): void
    {
        $messages = [];

        if (in_array(NurseLog::INTERVENTION_PRESCRIBED_MEDICINE, $interventions, true)) {
            foreach (['medicine_id', 'medicine_quantity', 'dosage'] as $field) {
                if (empty($validated[$field])) {
                    $messages[$field] = 'Medicine, quantity, and dosage are required when Prescribe / Issue Medicine is selected.';
                }
            }
        }

        if (in_array(NurseLog::INTERVENTION_MGH, $interventions, true) && empty($validated['mgh_status'])) {
            $messages['mgh_status'] = 'MGH status is required when MGH / May Go Home is selected.';
        }

        if (in_array(NurseLog::INTERVENTION_REST, $interventions, true)) {
            if (empty($validated['rest_minutes'])) {
                $messages['rest_minutes'] = 'Rest duration is required when Rest is selected.';
            }

            if (empty($validated['nurse_bed_id'])) {
                $messages['nurse_bed_id'] = 'A clinic bed is required when Rest is selected.';
            }
        }

        if (in_array(NurseLog::INTERVENTION_FIRST_AID, $interventions, true) && empty($validated['first_aid_details'])) {
            $messages['first_aid_details'] = 'First-aid details are required when First Aid is selected.';
        }

        if ($messages !== []) {
            throw ValidationException::withMessages($messages);
        }
    }

    private function countTodayByIntervention(string $type): int
    {
        return NurseLog::query()
            ->whereDate('time_in', today())
            ->where(function ($query) use ($type): void {
                $query->where('intervention_type', $type)
                    ->orWhereHas('interventions', function ($q) use ($type): void {
                        $q->where('intervention_type', $type);
                    });
            })
            ->count();
    }

    private function primaryIntervention(array $interventions): string
    {
        $priority = [
            NurseLog::INTERVENTION_REST,
            NurseLog::INTERVENTION_PRESCRIBED_MEDICINE,
            NurseLog::INTERVENTION_MGH,
            NurseLog::INTERVENTION_FIRST_AID,
            NurseLog::INTERVENTION_OTHER,
        ];

        foreach ($priority as $type) {
            if (in_array($type, $interventions, true)) {
                return $type;
            }
        }

        return NurseLog::INTERVENTION_OTHER;
    }
}
