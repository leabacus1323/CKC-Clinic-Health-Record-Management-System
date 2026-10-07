<?php

namespace App\Http\Controllers;

use App\Models\Doctors;
use App\Models\Medicine;
use App\Models\Patients;
use App\Models\PrescriptionRecord;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Symfony\Component\HttpFoundation\StreamedResponse;

class AdminPrescription extends Controller
{
    public function index(Request $request)
    {
        $search = trim((string) $request->query('search', ''));

        $prescriptions = PrescriptionRecord::query()
            ->with(['patient.user', 'doctor.user', 'medicine'])
            ->when($search !== '', function ($query) use ($search) {
                $query->where(function ($subQuery) use ($search) {
                    $subQuery->where('dosage', 'like', "%{$search}%")
                        ->orWhere('status', 'like', "%{$search}%")
                        ->orWhereHas('patient.user', fn ($userQuery) => $userQuery->where('name', 'like', "%{$search}%"))
                        ->orWhereHas('medicine', fn ($medicineQuery) => $medicineQuery->where('name', 'like', "%{$search}%"));
                });
            })
            ->latest()
            ->paginate(25)
            ->withQueryString();

        return view('admin.prescription.index', compact('prescriptions', 'search'));
    }

    public function export(): StreamedResponse
    {
        $fileName = 'prescriptions_' . now()->format('Y-m-d_His') . '.csv';

        return response()->streamDownload(function () {
            $handle = fopen('php://output', 'w');
            fwrite($handle, "\xEF\xBB\xBF");
            fputcsv($handle, [
                'ID', 'Patient Name', 'Patient Email', 'Medicine', 'Dosage',
                'Frequency', 'Duration', 'Instructions', 'Status',
                'Date Prescribed', 'Date Discontinued', 'Discontinuation Reason', 'Created At',
            ]);

            PrescriptionRecord::query()
                ->with(['patient.user', 'medicine'])
                ->orderBy('id')
                ->chunkById(250, function ($prescriptions) use ($handle) {
                    foreach ($prescriptions as $prescription) {
                        fputcsv($handle, [
                            $prescription->id,
                            $prescription->patient?->user?->name ?? '',
                            $prescription->patient?->user?->email ?? '',
                            $prescription->medicine?->name ?? '',
                            $prescription->dosage ?? '',
                            $prescription->frequency ?? '',
                            $prescription->duration ?? '',
                            $prescription->instruction ?? '',
                            $prescription->status ?? '',
                            $prescription->date_prescribed ?? '',
                            $prescription->date_discontinued ?? '',
                            $prescription->discontinuation_reason ?? '',
                            optional($prescription->created_at)->toDateTimeString() ?? '',
                        ]);
                    }
                });

            fclose($handle);
        }, $fileName, ['Content-Type' => 'text/csv; charset=UTF-8']);
    }

    public function create()
    {
        $patients = Patients::with('user')->orderBy('id')->get();
        $medicines = Medicine::where('quantity', '>', 0)->orderBy('name')->get();

        return view('admin.prescription.create', compact('patients', 'medicines'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'patient_id' => ['required', 'exists:patients,id'],
            'medicine_id' => ['required', 'exists:medicine,id'],
            'dosage' => ['required', 'string', 'max:255'],
            'frequency' => ['nullable', 'string', 'max:255'],
            'duration' => ['nullable', 'string', 'max:255'],
            'instruction' => ['nullable', 'string'],
            'date_prescribed' => ['nullable', 'date'],
            'status' => ['nullable', 'in:active,completed,discontinued'],
        ]);

        $doctor = Doctors::where('user_id', Auth::id())->first();

        PrescriptionRecord::create([
            ...$validated,
            'doctor_id' => $doctor?->id,
            'prescribed_by' => Auth::id(),
            'date_prescribed' => $validated['date_prescribed'] ?? now()->toDateString(),
            'status' => $validated['status'] ?? 'active',
        ]);

        return redirect()->route('admin.prescription.index')
            ->with('success', 'Prescription created successfully.');
    }

    public function edit(string $id)
    {
        $prescription = PrescriptionRecord::with(['patient.user', 'medicine'])->findOrFail($id);
        $patients = Patients::with('user')->orderBy('id')->get();
        $medicines = Medicine::orderBy('name')->get();

        return view('admin.prescription.edit', compact('prescription', 'patients', 'medicines'));
    }

    public function update(Request $request, string $id)
    {
        $prescription = PrescriptionRecord::findOrFail($id);
        $validated = $request->validate([
            'patient_id' => ['required', 'exists:patients,id'],
            'medicine_id' => ['required', 'exists:medicine,id'],
            'dosage' => ['required', 'string', 'max:255'],
            'frequency' => ['nullable', 'string', 'max:255'],
            'duration' => ['nullable', 'string', 'max:255'],
            'instruction' => ['nullable', 'string'],
            'date_prescribed' => ['nullable', 'date'],
            'status' => ['required', 'in:active,completed,discontinued'],
        ]);

        $prescription->update($validated);

        return redirect()->route('admin.prescription.index')
            ->with('success', 'Prescription updated successfully.');
    }

    public function destroy(string $id)
    {
        $prescription = PrescriptionRecord::findOrFail($id);
        $prescription->delete();

        return redirect()->route('admin.prescription.index')
            ->with('success', 'Prescription deleted successfully.');
    }
}
