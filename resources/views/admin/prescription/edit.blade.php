@extends('admin.layout.navigation')

@section('content')
<div class="max-w-4xl mx-auto p-6 bg-white rounded-xl shadow-sm">
    <div class="mb-6">
        <h1 class="text-2xl font-bold text-slate-900">Edit Prescription</h1>
        <p class="text-sm text-slate-500">Update the selected prescription record.</p>
    </div>

    @if ($errors->any())
        <div class="mb-5 rounded-lg border border-red-200 bg-red-50 p-4 text-sm text-red-700">
            <ul class="list-disc pl-5">
                @foreach ($errors->all() as $error)<li>{{ $error }}</li>@endforeach
            </ul>
        </div>
    @endif

    <form action="{{ route('admin.prescription.update', $prescription->id) }}" method="POST" class="space-y-5">
        @csrf
        @method('PUT')

        <div class="grid gap-5 md:grid-cols-2">
            <div>
                <label class="mb-2 block text-sm font-medium text-slate-700" for="patient_id">Patient</label>
                <select class="w-full rounded-lg border-slate-300" id="patient_id" name="patient_id" required>
                    @foreach($patients as $patient)
                        <option value="{{ $patient->id }}" @selected(old('patient_id', $prescription->patient_id) == $patient->id)>
                            {{ $patient->user->name ?? 'Patient #'.$patient->id }}
                        </option>
                    @endforeach
                </select>
            </div>
            <div>
                <label class="mb-2 block text-sm font-medium text-slate-700" for="medicine_id">Medicine</label>
                <select class="w-full rounded-lg border-slate-300" id="medicine_id" name="medicine_id" required>
                    @foreach($medicines as $medicine)
                        <option value="{{ $medicine->id }}" @selected(old('medicine_id', $prescription->medicine_id) == $medicine->id)>
                            {{ $medicine->name }}
                        </option>
                    @endforeach
                </select>
            </div>
            <div>
                <label class="mb-2 block text-sm font-medium text-slate-700" for="dosage">Dosage</label>
                <input class="w-full rounded-lg border-slate-300" id="dosage" name="dosage" value="{{ old('dosage', $prescription->dosage) }}" required>
            </div>
            <div>
                <label class="mb-2 block text-sm font-medium text-slate-700" for="frequency">Frequency</label>
                <input class="w-full rounded-lg border-slate-300" id="frequency" name="frequency" value="{{ old('frequency', $prescription->frequency) }}">
            </div>
            <div>
                <label class="mb-2 block text-sm font-medium text-slate-700" for="duration">Duration</label>
                <input class="w-full rounded-lg border-slate-300" id="duration" name="duration" value="{{ old('duration', $prescription->duration) }}">
            </div>
            <div>
                <label class="mb-2 block text-sm font-medium text-slate-700" for="date_prescribed">Date prescribed</label>
                <input class="w-full rounded-lg border-slate-300" type="date" id="date_prescribed" name="date_prescribed" value="{{ old('date_prescribed', $prescription->date_prescribed ? \Carbon\Carbon::parse($prescription->date_prescribed)->format('Y-m-d') : '') }}">
            </div>
            <div>
                <label class="mb-2 block text-sm font-medium text-slate-700" for="status">Status</label>
                <select class="w-full rounded-lg border-slate-300" id="status" name="status" required>
                    @foreach(['active' => 'Active', 'completed' => 'Completed', 'discontinued' => 'Discontinued'] as $value => $label)
                        <option value="{{ $value }}" @selected(old('status', $prescription->status ?? 'active') === $value)>{{ $label }}</option>
                    @endforeach
                </select>
            </div>
        </div>

        <div>
            <label class="mb-2 block text-sm font-medium text-slate-700" for="instruction">Instructions</label>
            <textarea class="w-full rounded-lg border-slate-300" id="instruction" name="instruction" rows="4">{{ old('instruction', $prescription->instruction) }}</textarea>
        </div>

        <div class="flex gap-3">
            <button class="rounded-lg bg-teal-700 px-5 py-2.5 font-semibold text-white hover:bg-teal-800" type="submit">Save Changes</button>
            <a class="rounded-lg border border-slate-300 px-5 py-2.5 font-semibold text-slate-700 hover:bg-slate-50" href="{{ route('admin.prescription.index') }}">Cancel</a>
        </div>
    </form>
</div>
@endsection
