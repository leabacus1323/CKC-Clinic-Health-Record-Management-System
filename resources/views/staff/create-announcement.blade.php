@extends('layouts.app')

@section('content')
<div class="container mx-auto px-4 py-8">
    <div class="bg-white rounded-lg shadow p-6">
        <div class="flex items-center justify-between mb-6">
            <div>
                <h1 class="text-2xl font-semibold text-gray-800">Send Announcement</h1>
                <p class="text-gray-600">Notify all users about the regular check-up schedule.</p>
            </div>
            <a href="{{ route('staff.announcements') }}" class="inline-flex items-center px-4 py-2 bg-gray-100 text-gray-800 rounded hover:bg-gray-200">
                Back to Announcements
            </a>
        </div>

        @if(session('success'))
            <div class="mb-4 p-4 rounded-lg bg-green-50 text-green-700 border border-green-200">
                {{ session('success') }}
            </div>
        @endif

        <form action="{{ route('staff.announcements.store') }}" method="POST">
            @csrf

            <div class="mb-4">
                <label class="block text-sm font-medium text-gray-700 mb-2" for="title">Title</label>
                <input id="title" name="title" value="{{ old('title') }}" type="text" class="w-full rounded-lg border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500" placeholder="e.g. Regular Check-up Reminder">
                @error('title')
                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                @enderror
            </div>

            <div class="mb-4">
                <label class="block text-sm font-medium text-gray-700 mb-2" for="message">Message</label>
                <textarea id="message" name="message" rows="5" class="w-full rounded-lg border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500" placeholder="Write the announcement here...">{{ old('message') }}</textarea>
                @error('message')
                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                @enderror
            </div>

            <button type="submit" class="inline-flex items-center justify-center px-6 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700 transition">
                Send Announcement
            </button>
        </form>
    </div>
</div>
@endsection
