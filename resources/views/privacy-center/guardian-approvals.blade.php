@extends('admin.layout.navigation')

@section('content')
<div class="max-w-7xl mx-auto space-y-6">
    <div>
        <h1 class="text-2xl font-bold text-gray-900 dark:text-white">Guardian Relationship Verification</h1>
        <p class="mt-1 text-sm text-gray-600 dark:text-gray-300">Confirm legal guardianship before a guardian can see a minor student's health profile or sign authorizations.</p>
    </div>

    @if(session('success'))
        <div class="rounded-lg bg-green-50 p-4 text-green-800">{{ session('success') }}</div>
    @endif
    @if($errors->any())
        <div class="rounded-lg bg-red-50 p-4 text-red-800"><ul class="list-disc pl-5">@foreach($errors->all() as $error)<li>{{ $error }}</li>@endforeach</ul></div>
    @endif

    <nav class="flex flex-wrap gap-2" aria-label="Guardian verification status">
        @foreach(['pending' => 'Pending', 'approved' => 'Approved', 'rejected' => 'Rejected'] as $key => $label)
            <a href="{{ route('guardian-approvals.index', ['status' => $key]) }}" class="rounded-lg px-4 py-2 text-sm font-semibold {{ $status === $key ? 'bg-blue-700 text-white' : 'bg-white text-gray-700 border border-gray-300' }}">{{ $label }}</a>
        @endforeach
    </nav>

    <div class="space-y-4">
        @forelse($links as $link)
            <article class="rounded-xl border border-gray-200 bg-white p-5 shadow-sm dark:border-gray-700 dark:bg-gray-800">
                <div class="grid gap-4 lg:grid-cols-3">
                    <div>
                        <p class="text-xs font-semibold uppercase tracking-wide text-gray-500">Student</p>
                        <p class="mt-1 font-semibold text-gray-900 dark:text-white">{{ $link->patient?->user?->name ?? 'Patient #'.$link->patient_id }}</p>
                        <p class="text-sm text-gray-500">DOB: {{ $link->patient?->user?->dob?->format('M d, Y') ?? 'Not recorded' }}</p>
                    </div>
                    <div>
                        <p class="text-xs font-semibold uppercase tracking-wide text-gray-500">Proposed guardian</p>
                        <p class="mt-1 font-semibold text-gray-900 dark:text-white">{{ $link->full_name }}</p>
                        <p class="text-sm text-gray-500">{{ $link->relationship }} · {{ $link->invited_email }}</p>
                        <p class="mt-1 text-xs text-green-700">Account accepted {{ $link->verified_at?->format('M d, Y h:i A') }}</p>
                    </div>
                    <div>
                        <p class="text-xs font-semibold uppercase tracking-wide text-gray-500">Verification</p>
                        @if($link->approved_at)
                            <p class="mt-1 text-sm font-semibold text-green-700">Approved {{ $link->approved_at->format('M d, Y h:i A') }}</p>
                            <p class="text-xs text-gray-500">By {{ $link->approvedBy?->name ?? 'administrator' }}</p>
                        @elseif(!$link->is_active)
                            <p class="mt-1 text-sm font-semibold text-red-700">Rejected / disabled</p>
                        @else
                            <p class="mt-1 text-sm font-semibold text-amber-700">Pending administrator verification</p>
                        @endif
                        @if($link->approval_notes)<p class="mt-2 text-sm text-gray-600">{{ $link->approval_notes }}</p>@endif
                    </div>
                </div>

                @if($status === 'pending')
                    <form method="POST" action="{{ route('guardian-approvals.update', $link) }}" class="mt-5 grid gap-3 border-t pt-5 md:grid-cols-[160px_1fr_auto]">
                        @csrf @method('PATCH')
                        <select name="decision" required class="rounded-lg border-gray-300"><option value="approve">Approve</option><option value="reject">Reject</option></select>
                        <input name="approval_notes" required minlength="10" placeholder="State the document or process used to verify the relationship" class="rounded-lg border-gray-300">
                        <button class="rounded-lg bg-blue-700 px-5 py-2 text-sm font-semibold text-white">Record decision</button>
                    </form>
                @endif
            </article>
        @empty
            <div class="rounded-xl border border-dashed border-gray-300 bg-white p-10 text-center text-gray-500">No guardian links match this status.</div>
        @endforelse
    </div>

    <div>{{ $links->links() }}</div>
</div>
@endsection
