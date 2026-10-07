@extends('layouts.app')
@section('content')
<div class="container mx-auto space-y-6 px-4 py-8">
    <h1 class="text-2xl font-semibold text-gray-900">Examination History</h1>
    <section class="rounded-lg border bg-white shadow-sm">
        <h2 class="border-b px-6 py-4 text-lg font-semibold">Physical Examinations</h2>
        <div class="divide-y">
            @forelse($physicalExaminations as $exam)
                <article class="p-6"><p class="font-medium text-gray-900">Physical examination #{{ $exam->id }}</p><p class="mt-1 text-sm text-gray-600">{{ optional($exam->created_at)->format('M d, Y g:i A') }}</p>@if($exam->notes)<p class="mt-2 text-sm text-gray-700">{{ $exam->notes }}</p>@endif</article>
            @empty <p class="p-6 text-gray-500">No physical examinations found.</p> @endforelse
        </div>
    </section>
    <section class="rounded-lg border bg-white shadow-sm">
        <h2 class="border-b px-6 py-4 text-lg font-semibold">Dental Examinations</h2>
        <div class="divide-y">
            @forelse($dentalExaminations as $exam)
                <article class="p-6"><p class="font-medium text-gray-900">Dental examination #{{ $exam->id }}</p><p class="mt-1 text-sm text-gray-600">{{ optional($exam->created_at)->format('M d, Y g:i A') }}</p>@if($exam->diagnosis)<p class="mt-2 text-sm text-gray-700">{{ $exam->diagnosis }}</p>@endif</article>
            @empty <p class="p-6 text-gray-500">No dental examinations found.</p> @endforelse
        </div>
    </section>
</div>
@endsection
