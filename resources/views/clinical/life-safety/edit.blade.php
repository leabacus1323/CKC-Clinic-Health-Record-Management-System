@extends('layouts.app')

@section('content')
<div class="mx-auto max-w-3xl px-4 py-8 sm:px-6">
    <a href="{{ route('clinical.life-safety.index') }}" class="text-sm font-semibold text-blue-600 hover:underline">← Back</a>
    <div class="mt-4 rounded-xl bg-white p-6 shadow">
        <h1 class="text-2xl font-bold">Life-Safety Alert: {{ $patient->user?->name }}</h1>
        <p class="mt-1 text-sm text-gray-600">Do not copy treatment notes. Publish only the minimum information a teacher needs to prevent or respond to an immediate emergency.</p>
        <form method="POST" action="{{ route('clinical.life-safety.update', $patient) }}" class="mt-6 space-y-5">@csrf @method('PUT')
            <label class="flex items-start gap-3 rounded-lg border p-4"><input type="checkbox" name="life_safety_visible_to_teachers" value="1" class="mt-1" @checked(old('life_safety_visible_to_teachers', $patient->life_safety_visible_to_teachers))><span><strong>Visible to teachers</strong><br><span class="text-sm text-gray-600">Teacher access is read-only and separately audited.</span></span></label>
            <div><label class="block text-sm font-semibold">Immediate life-safety alert</label><textarea name="life_safety_alert" rows="3" class="mt-1 w-full rounded-lg border-gray-300" maxlength="500">{{ old('life_safety_alert', $patient->life_safety_alert) }}</textarea><x-input-error :messages="$errors->get('life_safety_alert')" class="mt-2" /></div>
            <div><label class="block text-sm font-semibold">Emergency medication carried or stored</label><input name="emergency_medication" value="{{ old('emergency_medication', $patient->emergency_medication) }}" class="mt-1 w-full rounded-lg border-gray-300" maxlength="255"></div>
            <div><label class="block text-sm font-semibold">Emergency instructions</label><textarea name="life_safety_instructions" rows="6" class="mt-1 w-full rounded-lg border-gray-300" maxlength="2000">{{ old('life_safety_instructions', $patient->life_safety_instructions) }}</textarea></div>
            <div class="flex justify-end"><button class="rounded-lg bg-blue-600 px-5 py-2 font-semibold text-white hover:bg-blue-700">Save reviewed alert</button></div>
        </form>
    </div>
</div>
@endsection
