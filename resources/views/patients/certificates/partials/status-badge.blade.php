@switch($status)
    @case('pending')
        <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-yellow-100 text-yellow-800">
            <span class="mr-1">⏳</span> Pending
        </span>
        @break
    @case('approved')
        <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-blue-100 text-blue-800">
            <span class="mr-1">✓</span> Approved
        </span>
        @break
    @case('rejected')
        <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-red-100 text-red-800">
            <span class="mr-1">✕</span> Rejected
        </span>
        @break
    @case('issued')
        <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-green-100 text-green-800">
            <span class="mr-1">📄</span> Issued
        </span>
        @break
    @default
        <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-gray-100 text-gray-800">
            {{ ucfirst($status) }}
        </span>
@endswitch