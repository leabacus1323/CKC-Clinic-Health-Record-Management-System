@extends('admin.layout.navigation')

@section('content')
<form method="GET" action="{{ route('admin.doctors.index') }}" class="mb-4 flex flex-col gap-2 sm:flex-row">
    <input type="search" name="search" value="{{ $search ?? request('search') }}" placeholder="Search doctor, specialization, or clinic" class="w-full rounded-lg border-gray-300 sm:max-w-md">
    <button type="submit" class="rounded-lg bg-blue-600 px-4 py-2 text-white hover:bg-blue-700">Search</button>
    @if(request()->filled('search'))
        <a href="{{ route('admin.doctors.index') }}" class="rounded-lg border px-4 py-2 text-center text-gray-700">Clear</a>
    @endif
</form>

<div class="relative overflow-x-auto">
    @if (session('success'))
    <div id="alert-border-3" class="flex items-center p-4 mb-4 text-green-800 border-t-4 border-green-300 bg-green-50 dark:text-green-400 dark:bg-gray-800 dark:border-green-800" role="alert">
        <svg class="flex-shrink-0 w-4 h-4" aria-hidden="true" xmlns="http://www.w3.org/2000/svg" fill="currentColor" viewBox="0 0 20 20">
            <path d="M10 .5a9.5 9.5 0 1 0 9.5 9.5A9.51 9.51 0 0 0 10 .5ZM9.5 4a1.5 1.5 0 1 1 0 3 1.5 1.5 0 0 1 0-3ZM12 15H8a1 1 0 0 1 0-2h1v-3H8a1 1 0 0 1 0-2h2a1 1 0 0 1 1 1v4h1a1 1 0 0 1 0 2Z"/>
        </svg>
        <div class="ms-3 text-sm font-medium">
            {{ session('success') }}
        </div>
    </div>
    @endif
    @if ($errors->any())
        @foreach ($errors->all() as $error)
            <div id="alert-border-4" class="flex items-center p-4 mb-4 text-yellow-800 border-t-4 border-yellow-300 bg-yellow-50 dark:text-yellow-300 dark:bg-gray-800 dark:border-yellow-800" role="alert">
                <svg class="flex-shrink-0 w-4 h-4" aria-hidden="true" xmlns="http://www.w3.org/2000/svg" fill="currentColor" viewBox="0 0 20 20">
                <path d="M10 .5a9.5 9.5 0 1 0 9.5 9.5A9.51 9.51 0 0 0 10 .5ZM9.5 4a1.5 1.5 0 1 1 0 3 1.5 1.5 0 0 1 0-3ZM12 15H8a1 1 0 0 1 0-2h1v-3H8a1 1 0 0 1 0-2h2a1 1 0 0 1 1 1v4h1a1 1 0 0 1 0 2Z"/>
                </svg>
                <div class="ms-3 text-sm font-medium">
                    {{ $error }}
                </div>
            </div>
        @endforeach
    @endif
    <table class="w-full text-sm text-left rtl:text-right text-gray-500 dark:text-gray-400">
        <thead class="text-xs text-gray-700 uppercase bg-gray-50 dark:bg-gray-700 dark:text-gray-400">
            <tr>
                <th scope="col" class="px-6 py-4">
                    Id
                </th>
                <th scope="col" class="px-6 py-4">
                    First Name
                </th>
                <th scope="col" class="px-6 py-4">
                    Last Name
                </th>
                <th scope="col" class="px-6 py-4">
                    Phone Number
                </th>
                <th scope="col" class="px-6 py-4">
                    Specialization
                </th>
                <th scope="col" class="px-6 py-4">
                    Clinic Location
                </th>
                <th scope="col" class="px-6 py-4">
                    Status
                </th>
                <th scope="col" class="px-6 py-4">
                    Action
                </th>
            </tr>
        </thead>
        <tbody>
            @foreach($doctors as $doctor)
            <tr class="bg-white border-b dark:bg-gray-800 dark:border-gray-700">
                <th scope="row" class="px-6 py-4 font-medium text-gray-900 whitespace-nowrap dark:text-white">
                    {{ $doctor->id }}
                </th>
                <td class="px-6 py-4">
                    {{ $doctor->user->f_name }}
                </td>
                <td class="px-6 py-4">
                    {{ $doctor->user->l_name }}
                </td>
                <td class="px-6 py-4">
                    {{ $doctor->user->contact_no }}
                </td>
                <td class="px-6 py-4">
                    {{ $doctor->specialization }}
                </td>
                <td class="px-6 py-4">
                    {{ $doctor->address }}
                </td>
                <td class="px-6 py-4">
                    @if ($doctor->is_available == 1)
                        Available
                    @else
                        Not Available
                    @endif
                </td>
                <td class="flex items-center px-6 py-4">
                    <a href="{{ route('admin.doctors.edit', $doctor->id) }}" class="font-medium text-blue-600 dark:text-blue-500 hover:underline">Edit</a>
                    <a class="font-medium text-red-600 dark:text-red-500 hover:underline ms-3">
                        <form action="{{ route('admin.doctors.destroy', $doctor->id) }}" method="POST" style="display:inline-block;">
                            @csrf
                            @method('DELETE')
                            <button type="submit" class="font-medium text-red-600 dark:text-red-500 hover:underline ms-3r" onclick="return confirm('Are you sure?')">Remove</button>
                        </form>
                    </a>
                </td>
            </tr>
            @endforeach
        </tbody>
    </table>
</div>
<div class="mt-4">{{ $doctors->links() }}</div>
@endsection
