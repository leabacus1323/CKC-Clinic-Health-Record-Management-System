@extends('layouts.app')

@section('content')
<div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8 space-y-8">
    <div class="flex flex-col gap-4 sm:flex-row sm:items-center sm:justify-between">
        <div>
            <a href="{{ route('privacy-center.index') }}" class="text-sm font-medium text-blue-600 hover:underline">← Privacy & Compliance Center</a>
            <h1 class="mt-2 text-2xl font-bold text-gray-900 dark:text-white">{{ $patient->user?->name }}</h1>
            <p class="text-sm text-gray-600 dark:text-gray-300">
                {{ $patient->isStudentRegistry() ? 'Student registry' : 'Non-student registry' }} · {{ str_replace('_', ' ', $patient->privacy_regime) }}
                @if($patient->isStudentRegistry()) · {{ $patient->requiresGuardianConsent() ? 'Guardian-controlled consent' : 'Student-controlled consent' }} @endif
            </p>
        </div>
        <a href="{{ route('privacy-center.immunizations.index', $patient) }}" class="rounded-lg bg-emerald-600 px-4 py-2 text-center text-sm font-semibold text-white hover:bg-emerald-700">Immunization portal</a>
    </div>

    @if(session('success')) <div class="rounded-lg bg-green-50 p-4 text-green-800">{{ session('success') }}</div> @endif
    @if(session('info')) <div class="rounded-lg bg-blue-50 p-4 text-blue-800">{{ session('info') }}</div> @endif
    @if($errors->any())
        <div class="rounded-lg bg-red-50 p-4 text-red-800"><ul class="list-disc pl-5">@foreach($errors->all() as $error)<li>{{ $error }}</li>@endforeach</ul></div>
    @endif

    <section class="rounded-xl border border-gray-200 bg-white p-6 shadow-sm dark:border-gray-700 dark:bg-gray-800">
        <h2 class="text-lg font-semibold text-gray-900 dark:text-white">Immunization compliance</h2>
        <div class="mt-4 grid gap-4 md:grid-cols-2 xl:grid-cols-3">
            @forelse($compliance as $item)
                @php
                    $classes = match($item['status']) {
                        'compliant' => 'bg-green-50 border-green-200 text-green-900',
                        'pending_review' => 'bg-amber-50 border-amber-200 text-amber-900',
                        'not_due' => 'bg-blue-50 border-blue-200 text-blue-900',
                        'due_soon' => 'bg-orange-50 border-orange-200 text-orange-900',
                        default => 'bg-red-50 border-red-200 text-red-900',
                    };
                @endphp
                <div class="rounded-lg border p-4 {{ $classes }}">
                    <p class="font-semibold">{{ $item['requirement']->name }}</p>
                    <p class="mt-1 text-sm capitalize">{{ str_replace('_', ' ', $item['status']) }}</p>
                    <p class="mt-2 text-xs">Verified doses: {{ $item['verified_doses'] }} / {{ $item['required_doses'] }}</p>
                    @if($item['next_due_date'])<p class="mt-1 text-xs">Next deadline: {{ $item['next_due_date']->format('M d, Y') }}</p>@endif
                </div>
            @empty
                <p class="col-span-full text-sm text-gray-500">No active requirements are configured for this registry.</p>
            @endforelse
        </div>
    </section>

    <section class="grid gap-6 lg:grid-cols-2">
        <div class="rounded-xl border border-gray-200 bg-white p-6 shadow-sm dark:border-gray-700 dark:bg-gray-800">
            <h2 class="text-lg font-semibold text-gray-900 dark:text-white">Guardian access</h2>
            <p class="mt-1 text-sm text-gray-500">Guardian access follows the configured student-rights transfer rule; after rights transfer, explicit authorization is required.</p>
            <div class="mt-4 space-y-3">
                @forelse($patient->guardians as $guardian)
                    <div class="flex items-start justify-between gap-3 rounded-lg border border-gray-200 p-3">
                        <div>
                            <p class="font-medium text-gray-900">{{ $guardian->full_name }}</p>
                            <p class="text-xs text-gray-500">{{ $guardian->relationship }} · {{ $guardian->invited_email }}</p>
                            @if($guardian->approved_at)
                                <p class="mt-1 text-xs text-green-700">Relationship verified</p>
                            @elseif($guardian->verified_at)
                                <p class="mt-1 text-xs text-blue-700">Accepted; awaiting administrator verification</p>
                            @else
                                <p class="mt-1 text-xs text-amber-700">Pending account acceptance</p>
                            @endif
                        </div>
                        @if($canManageConsent || auth()->id() === $patient->user_id)
                            <form method="POST" action="{{ route('privacy-center.guardians.destroy', [$patient, $guardian]) }}">
                                @csrf @method('DELETE')
                                <button class="text-xs font-semibold text-red-600 hover:underline">Disable</button>
                            </form>
                        @endif
                    </div>
                @empty
                    <p class="text-sm text-gray-500">No guardian is linked.</p>
                @endforelse
            </div>

            @if(auth()->id() === $patient->user_id && $patient->isStudentRegistry())
                <form method="POST" action="{{ route('privacy-center.guardians.store', $patient) }}" class="mt-5 space-y-3 border-t pt-5">
                    @csrf
                    <h3 class="font-medium text-gray-900">Invite a guardian</h3>
                    <div class="grid gap-3 sm:grid-cols-2">
                        <input name="full_name" value="{{ old('full_name') }}" required placeholder="Full legal name" class="rounded-lg border-gray-300">
                        <input name="email" type="email" value="{{ old('email') }}" required placeholder="Guardian account email" class="rounded-lg border-gray-300">
                        <input name="relationship" value="{{ old('relationship') }}" required placeholder="Relationship" class="rounded-lg border-gray-300">
                        <label class="flex items-center gap-2 text-sm"><input type="checkbox" name="can_manage_consents" value="1" checked> May sign authorizations while student is a minor</label>
                    </div>
                    <button class="rounded-lg bg-gray-900 px-4 py-2 text-sm font-semibold text-white">Save invitation</button>
                </form>
            @endif
        </div>

        <div class="rounded-xl border border-gray-200 bg-white p-6 shadow-sm dark:border-gray-700 dark:bg-gray-800">
            <h2 class="text-lg font-semibold text-gray-900 dark:text-white">Active digital authorizations</h2>
            <div class="mt-4 space-y-3">
                @forelse($patient->dataAuthorizations as $authorization)
                    <div class="rounded-lg border border-gray-200 p-4">
                        <div class="flex items-start justify-between gap-3">
                            <div><p class="font-medium text-gray-900">{{ $authorization->recipient_name }}</p><p class="text-xs text-gray-500">{{ $authorization->recipient_email }}</p></div>
                            <span class="rounded-full px-2 py-1 text-xs font-semibold {{ $authorization->status === 'active' ? 'bg-green-100 text-green-800' : 'bg-gray-100 text-gray-700' }}">{{ ucfirst($authorization->status) }}</span>
                        </div>
                        <p class="mt-2 text-sm text-gray-600">{{ $authorization->purpose }}</p>
                        <div class="mt-2 flex flex-wrap gap-1">@foreach($authorization->scopes as $scope)<span class="rounded bg-blue-50 px-2 py-1 text-xs text-blue-700">{{ $scopes[$scope] ?? $scope }}</span>@endforeach</div>
                        <a href="{{ route('privacy-center.authorizations.show', [$patient, $authorization]) }}" class="mt-3 inline-flex text-xs font-semibold text-blue-700 hover:underline">View signed form</a>
                        @if($authorization->status === 'active' && $canManageConsent)
                            <form method="POST" action="{{ route('privacy-center.authorizations.revoke', [$patient, $authorization]) }}" class="mt-3 flex gap-2">
                                @csrf @method('PATCH')
                                <input name="revocation_reason" required placeholder="Reason for revocation" class="min-w-0 flex-1 rounded-lg border-gray-300 text-sm">
                                <button class="rounded-lg bg-red-600 px-3 py-2 text-xs font-semibold text-white">Revoke</button>
                            </form>
                        @endif
                    </div>
                @empty
                    <p class="text-sm text-gray-500">No authorizations have been signed.</p>
                @endforelse
            </div>
        </div>
    </section>

    @if($canManageConsent)
        <section class="rounded-xl border border-blue-200 bg-blue-50 p-6">
            <h2 class="text-lg font-semibold text-blue-950">Create granular digital authorization</h2>
            <p class="mt-1 text-sm text-blue-800">Specify the recipient, purpose, exact data categories, and expiration date.</p>
            <form method="POST" action="{{ route('privacy-center.authorizations.store', $patient) }}" class="mt-5 space-y-4">
                @csrf
                <div class="grid gap-4 md:grid-cols-2">
                    <label class="text-sm font-medium text-gray-800">Recipient name<input name="recipient_name" required value="{{ old('recipient_name') }}" class="mt-1 block w-full rounded-lg border-gray-300"></label>
                    <label class="text-sm font-medium text-gray-800">Recipient email<input name="recipient_email" type="email" required value="{{ old('recipient_email') }}" class="mt-1 block w-full rounded-lg border-gray-300"></label>
                    <label class="md:col-span-2 text-sm font-medium text-gray-800">Purpose<textarea name="purpose" required rows="3" class="mt-1 block w-full rounded-lg border-gray-300">{{ old('purpose') }}</textarea></label>
                    <label class="text-sm font-medium text-gray-800">Expiration date<input name="expires_at" type="date" required value="{{ old('expires_at') }}" class="mt-1 block w-full rounded-lg border-gray-300"><span class="mt-1 block text-xs text-gray-500">Required; maximum {{ config('health_privacy.consent_max_months', 24) }} months.</span></label>
                    <label class="text-sm font-medium text-gray-800">Electronic signature<input name="signature_name" required value="{{ old('signature_name', auth()->user()->name) }}" class="mt-1 block w-full rounded-lg border-gray-300"></label>
                    <label class="md:col-span-2 text-sm font-medium text-gray-800">Confirm your password<input name="current_password" type="password" required autocomplete="current-password" class="mt-1 block w-full rounded-lg border-gray-300"><span class="mt-1 block text-xs text-gray-500">Password re-entry confirms the identity of the person signing.</span></label>
                </div>
                <fieldset><legend class="text-sm font-semibold text-gray-900">Authorized data categories</legend><div class="mt-2 grid gap-2 sm:grid-cols-2 lg:grid-cols-4">@foreach($scopes as $key => $label)<label class="flex items-start gap-2 rounded-lg bg-white p-3 text-sm"><input type="checkbox" name="scopes[]" value="{{ $key }}"><span>{{ $label }}</span></label>@endforeach</div></fieldset>
                <div class="space-y-3 rounded-lg border border-blue-200 bg-white p-4">
                    <label class="flex items-start gap-2 text-sm text-gray-800"><input type="checkbox" name="affirmation" value="1" required class="mt-1"><span>I confirm that I am legally authorized to sign for this profile and that this electronic signature is binding.</span></label>
                    <label class="flex items-start gap-2 text-sm text-gray-800"><input type="checkbox" name="revocation_acknowledgement" value="1" required class="mt-1"><span>I understand that I may revoke this authorization in writing or through this portal, except to the extent action has already been taken in reliance on it.</span></label>
                    <label class="flex items-start gap-2 text-sm text-gray-800"><input type="checkbox" name="redisclosure_acknowledgement" value="1" required class="mt-1"><span>I understand that information disclosed to the named recipient may be subject to redisclosure under applicable law, and access is not a condition of treatment or enrollment unless legally permitted.</span></label>
                </div>
                <p class="text-xs text-blue-800">Authorization form version: {{ config('health_privacy.authorization_version', '1.0') }}</p>
                <button class="rounded-lg bg-blue-700 px-5 py-2.5 text-sm font-semibold text-white hover:bg-blue-800">Sign and activate authorization</button>
            </form>
        </section>
    @elseif($patient->requiresGuardianConsent())
        <div class="rounded-lg bg-amber-50 p-4 text-sm text-amber-900">A verified legal guardian must sign authorizations for this minor student profile.</div>
    @endif
</div>
@endsection
