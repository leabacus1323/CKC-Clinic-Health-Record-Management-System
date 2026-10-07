@extends('admin.layout.navigation')
@section('content')
<div class="max-w-3xl mx-auto">
    <a href="{{ route('break-glass.index') }}" class="text-sm font-medium text-blue-600 hover:underline">← Emergency access list</a>
    <div class="mt-4 rounded-xl border-2 border-red-300 bg-white p-6 shadow-sm">
        <div class="rounded-lg bg-red-700 p-4 text-white"><h1 class="text-xl font-bold">Activate emergency access</h1><p class="mt-1 text-sm text-red-100">Patient: {{ $patient->user?->name }} · Record #{{ $patient->id }}</p></div>
        @if($errors->any()) <div class="mt-4 rounded-lg bg-red-50 p-4 text-red-800"><ul class="list-disc pl-5">@foreach($errors->all() as $error)<li>{{ $error }}</li>@endforeach</ul></div> @endif
        <form method="POST" action="{{ route('break-glass.store', $patient) }}" class="mt-6 space-y-5">
            @csrf
            <label class="block text-sm font-medium text-gray-800">Describe the actual, impending, or imminent threat<textarea name="threat_description" rows="4" required class="mt-1 block w-full rounded-lg border-gray-300" placeholder="State the specific health or safety threat, who is at risk, and why it is urgent.">{{ old('threat_description') }}</textarea></label>
            <label class="block text-sm font-medium text-gray-800">Why is this limited disclosure necessary?<textarea name="justification" rows="4" required class="mt-1 block w-full rounded-lg border-gray-300" placeholder="Explain why allergy, blood type, condition, or emergency contact data is needed now.">{{ old('justification') }}</textarea></label>
            <label class="block text-sm font-medium text-gray-800">External recipient or responding party, when applicable<input name="recipient_party" value="{{ old('recipient_party') }}" class="mt-1 block w-full rounded-lg border-gray-300" placeholder="Example: responding EMS team"></label>
            <label class="block text-sm font-medium text-gray-800">Confirm your password<input name="current_password" type="password" required autocomplete="current-password" class="mt-1 block w-full rounded-lg border-gray-300" placeholder="Required to activate emergency access"></label>
            <div class="rounded-lg border border-amber-200 bg-amber-50 p-4 text-sm text-amber-900">This action will reveal only identity, emergency contact, allergies/blood type, and medical conditions. The grant expires after {{ config('health_privacy.break_glass_minutes', 10) }} minutes.</div>
            <label class="flex items-start gap-3 text-sm text-gray-800"><input type="checkbox" name="confirmation" value="1" required class="mt-1"><span>I certify that this is a genuine emergency and understand that my identity, reason, IP address, browser, time, and every subsequent view will be recorded in an append-only audit log.</span></label>
            <button class="w-full rounded-lg bg-red-700 px-5 py-3 font-semibold text-white hover:bg-red-800">Activate limited emergency access</button>
        </form>
    </div>
</div>
@endsection
