@extends('layouts.app')

@section('content')
<div class="container mx-auto px-4 py-8">
    <div class="bg-white rounded-lg shadow p-6">
        <div class="flex items-center justify-between mb-6">
            <div>
                <h1 class="text-2xl font-semibold text-gray-800">Announcements</h1>
                <p class="text-gray-600">All reminder messages sent to users.</p>
            </div>
            <a href="{{ route('staff.announcements.create') }}" class="inline-flex items-center px-4 py-2 bg-blue-600 text-white rounded hover:bg-blue-700">
                New Announcement
            </a>
        </div>

        @if(session('success'))
            <div class="mb-4 p-4 rounded-lg bg-green-50 text-green-700 border border-green-200">
                {{ session('success') }}
            </div>
        @endif

        @if($announcements->count())
            <div class="space-y-4">
                @foreach($announcements as $announcement)
                    <div class="border rounded-lg p-4 bg-gray-50">
                        <div class="flex items-start justify-between">
                            <div>
                                <h2 class="text-lg font-semibold text-gray-800">{{ $announcement->title }}</h2>
                                <p class="text-sm text-gray-500">Sent {{ $announcement->created_at->diffForHumans() }}</p>
                            </div>
                            @if($announcement->creator)
                                <span class="text-sm text-gray-600">By {{ $announcement->creator->name }}</span>
                            @endif
                        </div>
                        <p class="mt-3 text-gray-700 whitespace-pre-line">{{ $announcement->message }}</p>
                    </div>
                @endforeach
            </div>

            <div class="mt-6">
                {{ $announcements->links() }}
            </div>
        @else
            <div class="p-6 text-center text-gray-600 bg-gray-50 rounded-lg">
                No announcements have been created yet.
            </div>
        @endif
    </div>
</div>
@endsection
