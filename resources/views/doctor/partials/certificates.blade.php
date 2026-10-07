<div class="space-y-4">
    <div class="flex items-center justify-between">
        <div>
            <h2 class="text-2xl font-semibold text-gray-900">Certificate Requests</h2>
            <p class="text-sm text-gray-600">Review medical certificate requests assigned to the clinic.</p>
        </div>
        <a href="{{ route('doctor.certificate-requests') }}" class="rounded bg-blue-600 px-4 py-2 text-sm font-medium text-white hover:bg-blue-700">Open full list</a>
    </div>

    <div class="overflow-hidden rounded-lg border border-gray-200 bg-white">
        @forelse($certificateRequests as $certificateRequest)
            <div class="flex items-center justify-between border-b border-gray-100 p-4 last:border-b-0">
                <div>
                    <p class="font-medium text-gray-900">{{ optional(optional($certificateRequest->patient)->user)->name ?? 'Patient' }}</p>
                    <p class="text-sm text-gray-600">{{ optional($certificateRequest->certificateType)->name ?? 'Medical certificate' }}</p>
                </div>
                <span class="rounded-full bg-gray-100 px-3 py-1 text-xs font-medium text-gray-700">{{ ucfirst($certificateRequest->status ?? 'pending') }}</span>
            </div>
        @empty
            <p class="p-6 text-center text-gray-500">No certificate requests found.</p>
        @endforelse
    </div>
</div>
