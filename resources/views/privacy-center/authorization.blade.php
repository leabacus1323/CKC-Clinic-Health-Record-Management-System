@extends('layouts.app')

@section('content')
<div class="mx-auto max-w-4xl px-4 py-8 sm:px-6 lg:px-8">
    <div class="mb-6 flex flex-col gap-3 sm:flex-row sm:items-center sm:justify-between print:hidden">
        <a href="{{ route('privacy-center.show', $patient) }}" class="text-sm font-semibold text-blue-700 hover:underline">← Return to profile</a>
        <button type="button" onclick="window.print()" class="rounded-lg bg-gray-900 px-4 py-2 text-sm font-semibold text-white">Print signed form</button>
    </div>

    <article class="rounded-xl border border-gray-200 bg-white p-6 shadow-sm sm:p-10">
        <header class="border-b border-gray-200 pb-6">
            <p class="text-xs font-semibold uppercase tracking-wide text-blue-700">Electronic health-information authorization</p>
            <h1 class="mt-2 text-2xl font-bold text-gray-900">Signed authorization record</h1>
            <p class="mt-2 text-sm text-gray-600">Record ID: {{ $authorization->public_id }}</p>
        </header>

        <dl class="mt-6 grid gap-5 text-sm sm:grid-cols-2">
            <div><dt class="text-gray-500">Profile subject</dt><dd class="font-semibold text-gray-900">{{ $patient->user?->name }}</dd></div>
            <div><dt class="text-gray-500">Grantor</dt><dd class="font-semibold text-gray-900">{{ $authorization->grantor?->name }} &lt;{{ $authorization->grantor?->email }}&gt;</dd></div>
            <div><dt class="text-gray-500">Authorized recipient</dt><dd class="font-semibold text-gray-900">{{ $authorization->recipient_name }} &lt;{{ $authorization->recipient_email }}&gt;</dd></div>
            <div><dt class="text-gray-500">Status</dt><dd class="font-semibold capitalize text-gray-900">{{ $authorization->status }}</dd></div>
            <div><dt class="text-gray-500">Effective</dt><dd class="font-semibold text-gray-900">{{ $authorization->effective_at?->format('M d, Y h:i A') }}</dd></div>
            <div><dt class="text-gray-500">Expires</dt><dd class="font-semibold text-gray-900">{{ $authorization->expires_at?->format('M d, Y') }}</dd></div>
        </dl>

        <section class="mt-8">
            <h2 class="font-semibold text-gray-900">Authorized categories</h2>
            <div class="mt-3 flex flex-wrap gap-2">
                @foreach($authorization->scopes as $scope)
                    <span class="rounded-full bg-blue-50 px-3 py-1 text-xs font-semibold text-blue-800">{{ config('health_privacy.scopes.'.$scope, $scope) }}</span>
                @endforeach
            </div>
        </section>

        <section class="mt-8 rounded-lg bg-gray-50 p-5">
            <h2 class="font-semibold text-gray-900">Exact authorization text</h2>
            <p class="mt-3 whitespace-pre-line text-sm leading-6 text-gray-700">{{ $authorization->authorization_text }}</p>
        </section>

        <section class="mt-8 grid gap-5 border-t border-gray-200 pt-6 text-sm sm:grid-cols-2">
            <div><p class="text-gray-500">Electronic signature</p><p class="mt-1 font-semibold text-gray-900">{{ $authorization->signature_name }}</p></div>
            <div><p class="text-gray-500">Form version</p><p class="mt-1 font-semibold text-gray-900">{{ $authorization->authorization_version }}</p></div>
            <div><p class="text-gray-500">Signature integrity</p><p class="mt-1 font-semibold {{ $authorization->hasValidSignature() ? 'text-green-700' : 'text-red-700' }}">{{ $authorization->hasValidSignature() ? 'Verified' : 'Verification failed' }}</p></div>
            <div class="sm:col-span-2"><p class="text-gray-500">Verification fingerprint</p><p class="mt-1 break-all font-mono text-xs text-gray-700">{{ $authorization->signature_hash }}</p></div>
        </section>

        @if($authorization->revoked_at)
            <section class="mt-8 rounded-lg border border-red-200 bg-red-50 p-5 text-sm text-red-900">
                <p class="font-semibold">Revoked {{ $authorization->revoked_at->format('M d, Y h:i A') }}</p>
                <p class="mt-1">{{ $authorization->revocation_reason }}</p>
            </section>
        @endif
    </article>
</div>
@endsection
