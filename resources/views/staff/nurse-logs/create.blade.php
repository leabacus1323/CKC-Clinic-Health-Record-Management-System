@extends('layouts.app')

@section('content')
<div class="container mx-auto max-w-5xl px-4 py-8">
    <div class="mb-6 flex items-center justify-between">
        <div>
            <h1 class="text-3xl font-bold text-gray-800">Create Nurse Log</h1>
            <p class="text-gray-600">Record patient visit details, time in/out, complaint, and intervention.</p>
        </div>
        <a href="{{ route('staff.nurse-logs.index') }}" class="rounded-lg border px-4 py-2 text-sm font-semibold text-gray-700 hover:bg-gray-50">Back to Nurse Logs</a>
    </div>

    @if($errors->any())
        <div class="mb-6 rounded-lg border border-red-200 bg-red-50 p-4 text-red-800">
            <p class="font-semibold">Please correct the following:</p>
            <ul class="mt-2 list-disc pl-5 text-sm">
                @foreach($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    <div class="mb-6 grid grid-cols-1 gap-4 md:grid-cols-3">
        <div class="rounded-lg border bg-white p-4 shadow-sm">
            <p class="text-sm text-gray-500">Total Clinic Beds</p>
            <p class="text-2xl font-bold text-gray-800">{{ $bedSummary['total'] }}</p>
        </div>
        <div class="rounded-lg border bg-white p-4 shadow-sm">
            <p class="text-sm text-gray-500">Available Beds</p>
            <p class="text-2xl font-bold text-green-700">{{ $bedSummary['available'] }}</p>
        </div>
        <div class="rounded-lg border bg-white p-4 shadow-sm">
            <p class="text-sm text-gray-500">Occupied Beds</p>
            <p class="text-2xl font-bold text-orange-600">{{ $bedSummary['occupied'] }}</p>
        </div>
    </div>

    <form method="POST" action="{{ route('staff.nurse-logs.store') }}" class="space-y-6 rounded-lg border bg-white p-6 shadow-sm">
        @csrf

        <div class="grid grid-cols-1 gap-4 md:grid-cols-2">
            <div>
                <label class="mb-1 block text-sm font-semibold text-gray-700">Patient</label>
                <select name="patient_id" required class="w-full rounded-lg border-gray-300 text-sm focus:border-teal-500 focus:ring-teal-500">
                    <option value="">Select patient</option>
                    @foreach($patients as $patient)
                        <option value="{{ $patient->id }}" @selected(old('patient_id') == $patient->id)>
                            {{ $patient->user?->name ?? 'Patient #' . $patient->id }} @if($patient->school_id) — {{ $patient->school_id }} @endif
                        </option>
                    @endforeach
                </select>
            </div>
            <div class="grid grid-cols-1 gap-4 sm:grid-cols-2">
                <div>
                    <label class="mb-1 block text-sm font-semibold text-gray-700">Time In</label>
                    <input type="datetime-local" name="time_in" value="{{ old('time_in', now()->format('Y-m-d\\TH:i')) }}" required class="w-full rounded-lg border-gray-300 text-sm focus:border-teal-500 focus:ring-teal-500">
                </div>
                <div>
                    <label class="mb-1 block text-sm font-semibold text-gray-700">Time Out</label>
                    <input type="datetime-local" name="time_out" value="{{ old('time_out') }}" class="w-full rounded-lg border-gray-300 text-sm focus:border-teal-500 focus:ring-teal-500">
                    <p class="mt-1 text-xs text-gray-500">Leave blank while patient is still in clinic.</p>
                </div>
            </div>
        </div>

        <div>
            <label class="mb-1 block text-sm font-semibold text-gray-700">Reason / Complaint</label>
            <textarea name="complaint" rows="3" required class="w-full rounded-lg border-gray-300 text-sm focus:border-teal-500 focus:ring-teal-500" placeholder="Example: Headache, stomachache, dizziness, injury, fever...">{{ old('complaint') }}</textarea>
        </div>

        <div class="rounded-lg border border-teal-200 bg-teal-50 p-4">
            <label class="mb-2 block text-sm font-semibold text-gray-700">Intervention/s Performed</label>
            <p class="mb-3 text-xs text-teal-800">Select one or more interventions used during this visit. Example: Rest + Medicine, First Aid + MGH, or Rest + First Aid.</p>
            @php($oldInterventions = old('interventions', []))
            <div class="grid grid-cols-1 gap-3 md:grid-cols-2">
                <label class="flex items-center gap-3 rounded-lg border bg-white p-3 text-sm font-semibold text-gray-700">
                    <input type="checkbox" name="interventions[]" value="prescribed_medicine" class="intervention-checkbox rounded border-gray-300 text-teal-600 focus:ring-teal-500" @checked(in_array('prescribed_medicine', $oldInterventions, true))>
                    Prescribe / Issue Medicine
                </label>
                <label class="flex items-center gap-3 rounded-lg border bg-white p-3 text-sm font-semibold text-gray-700">
                    <input type="checkbox" name="interventions[]" value="mgh" class="intervention-checkbox rounded border-gray-300 text-teal-600 focus:ring-teal-500" @checked(in_array('mgh', $oldInterventions, true))>
                    MGH / May Go Home
                </label>
                <label class="flex items-center gap-3 rounded-lg border bg-white p-3 text-sm font-semibold text-gray-700">
                    <input type="checkbox" name="interventions[]" value="rest" class="intervention-checkbox rounded border-gray-300 text-teal-600 focus:ring-teal-500" @checked(in_array('rest', $oldInterventions, true))>
                    Rest
                </label>
                <label class="flex items-center gap-3 rounded-lg border bg-white p-3 text-sm font-semibold text-gray-700">
                    <input type="checkbox" name="interventions[]" value="first_aid" class="intervention-checkbox rounded border-gray-300 text-teal-600 focus:ring-teal-500" @checked(in_array('first_aid', $oldInterventions, true))>
                    First Aid
                </label>
                <label class="flex items-center gap-3 rounded-lg border bg-white p-3 text-sm font-semibold text-gray-700">
                    <input type="checkbox" name="interventions[]" value="other" class="intervention-checkbox rounded border-gray-300 text-teal-600 focus:ring-teal-500" @checked(in_array('other', $oldInterventions, true))>
                    Other Intervention
                </label>
            </div>
            <p class="mt-2 text-xs text-gray-600">At least one intervention must be selected.</p>
        </div>

        <div id="medicine-section" data-intervention-section="prescribed_medicine" class="hidden rounded-lg border border-blue-200 bg-blue-50 p-4">
            <h2 class="mb-3 font-semibold text-blue-900">Medicine Prescription Details</h2>
            <div class="grid grid-cols-1 gap-4 md:grid-cols-2">
                <div>
                    <label class="mb-1 block text-sm font-semibold text-gray-700">Available Medicine</label>
                    <select name="medicine_id" class="w-full rounded-lg border-gray-300 text-sm focus:border-blue-500 focus:ring-blue-500">
                        <option value="">Select medicine</option>
                        @foreach($medicines as $medicine)
                            <option value="{{ $medicine->id }}" @selected(old('medicine_id') == $medicine->id)>
                                {{ $medicine->name }} {{ $medicine->dosage ? '(' . $medicine->dosage . ')' : '' }} — Stock: {{ $medicine->quantity }} {{ $medicine->unit }}
                            </option>
                        @endforeach
                    </select>
                </div>
                <div>
                    <label class="mb-1 block text-sm font-semibold text-gray-700">Quantity Issued</label>
                    <input type="number" name="medicine_quantity" min="1" value="{{ old('medicine_quantity', 1) }}" class="w-full rounded-lg border-gray-300 text-sm focus:border-blue-500 focus:ring-blue-500">
                </div>
                <div>
                    <label class="mb-1 block text-sm font-semibold text-gray-700">Dosage</label>
                    <input type="text" name="dosage" value="{{ old('dosage') }}" placeholder="Example: 500 mg" class="w-full rounded-lg border-gray-300 text-sm focus:border-blue-500 focus:ring-blue-500">
                </div>
                <div>
                    <label class="mb-1 block text-sm font-semibold text-gray-700">Frequency</label>
                    <input type="text" name="frequency" value="{{ old('frequency') }}" placeholder="Example: Every 6 hours" class="w-full rounded-lg border-gray-300 text-sm focus:border-blue-500 focus:ring-blue-500">
                </div>
                <div>
                    <label class="mb-1 block text-sm font-semibold text-gray-700">Duration</label>
                    <input type="text" name="duration" value="{{ old('duration') }}" placeholder="Example: 1 day or as needed" class="w-full rounded-lg border-gray-300 text-sm focus:border-blue-500 focus:ring-blue-500">
                </div>
                <div>
                    <label class="mb-1 block text-sm font-semibold text-gray-700">Prescription Instruction</label>
                    <input type="text" name="prescription_instruction" value="{{ old('prescription_instruction') }}" placeholder="Special instruction for the patient" class="w-full rounded-lg border-gray-300 text-sm focus:border-blue-500 focus:ring-blue-500">
                </div>
            </div>
            <p class="mt-3 text-xs text-blue-800">Saving this log will create a prescription record and deduct the issued quantity from medicine inventory.</p>
        </div>

        <div id="mgh-section" data-intervention-section="mgh" class="hidden rounded-lg border border-purple-200 bg-purple-50 p-4">
            <label class="mb-1 block text-sm font-semibold text-gray-700">MGH Status / Note</label>
            <input type="text" name="mgh_status" value="{{ old('mgh_status', 'MGH - May Go Home') }}" class="w-full rounded-lg border-gray-300 text-sm focus:border-purple-500 focus:ring-purple-500">
        </div>

        <div id="rest-section" data-intervention-section="rest" class="hidden rounded-lg border border-orange-200 bg-orange-50 p-4">
            <h2 class="mb-3 font-semibold text-orange-900">Rest and Bed Assignment</h2>
            <div class="grid grid-cols-1 gap-4 md:grid-cols-2">
                <div>
                    <label class="mb-1 block text-sm font-semibold text-gray-700">Rest Duration in Minutes</label>
                    <input type="number" name="rest_minutes" min="1" max="480" value="{{ old('rest_minutes', 30) }}" class="w-full rounded-lg border-gray-300 text-sm focus:border-orange-500 focus:ring-orange-500">
                </div>
                <div>
                    <label class="mb-1 block text-sm font-semibold text-gray-700">Available Bed</label>
                    <select name="nurse_bed_id" class="w-full rounded-lg border-gray-300 text-sm focus:border-orange-500 focus:ring-orange-500">
                        <option value="">Select available bed</option>
                        @foreach($beds as $bed)
                            @php($occupiedLog = $bed->activeRestLog)
                            <option value="{{ $bed->id }}" @selected(old('nurse_bed_id') == $bed->id) @disabled($occupiedLog !== null)>
                                {{ $bed->name }}{{ $bed->location ? ' — ' . $bed->location : '' }}
                                @if($occupiedLog)
                                    — Occupied by {{ $occupiedLog->patient?->user?->name ?? 'Patient #' . $occupiedLog->patient_id }}
                                @else
                                    — Available
                                @endif
                            </option>
                        @endforeach
                    </select>
                    <p class="mt-1 text-xs text-orange-800">A bed becomes available again after the nurse log time-out is recorded.</p>
                </div>
            </div>
        </div>

        <div id="first-aid-section" data-intervention-section="first_aid" class="hidden rounded-lg border border-red-200 bg-red-50 p-4">
            <label class="mb-1 block text-sm font-semibold text-gray-700">First Aid Intervention Performed</label>
            <textarea name="first_aid_details" rows="3" class="w-full rounded-lg border-gray-300 text-sm focus:border-red-500 focus:ring-red-500" placeholder="Enter the first-aid care given, such as wound cleaning, cold compress, bandage, or other immediate care.">{{ old('first_aid_details') }}</textarea>
        </div>

        <div>
            <label class="mb-1 block text-sm font-semibold text-gray-700">General Intervention Notes</label>
            <textarea name="intervention_notes" rows="3" class="w-full rounded-lg border-gray-300 text-sm focus:border-teal-500 focus:ring-teal-500" placeholder="Additional observation, advice, or follow-up instruction.">{{ old('intervention_notes') }}</textarea>
        </div>

        <div class="flex justify-end gap-3 border-t pt-6">
            <a href="{{ route('staff.nurse-logs.index') }}" class="rounded-lg border px-4 py-2 text-sm font-semibold text-gray-700 hover:bg-gray-50">Cancel</a>
            <button type="submit" class="rounded-lg bg-teal-600 px-5 py-2 text-sm font-semibold text-white hover:bg-teal-700">Save Nurse Log</button>
        </div>
    </form>
</div>

<script>
    function updateInterventionSections() {
        const selected = Array.from(document.querySelectorAll('.intervention-checkbox:checked')).map(input => input.value);

        document.querySelectorAll('[data-intervention-section]').forEach(section => {
            section.classList.toggle('hidden', !selected.includes(section.dataset.interventionSection));
        });
    }

    document.querySelectorAll('.intervention-checkbox').forEach(checkbox => {
        checkbox.addEventListener('change', updateInterventionSections);
    });

    updateInterventionSections();
</script>
@endsection
