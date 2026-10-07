@extends('admin.layout.navigation')

@section('content')
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

    <form method="GET" action="{{ route('admin.users.index') }}" class="mb-4 flex flex-col gap-2 sm:flex-row">
        <input type="search" name="search" value="{{ $search ?? request('search') }}" class="w-full rounded-lg border-gray-300 sm:max-w-md" placeholder="Search users by name or email">
        <button type="submit" class="rounded-lg bg-blue-600 px-4 py-2 text-white hover:bg-blue-700">Search</button>
        @if(request()->filled('search'))
            <a href="{{ route('admin.users.index') }}" class="rounded-lg border px-4 py-2 text-center text-gray-700">Clear</a>
        @endif
    </form>

    <table class="w-full text-sm text-left rtl:text-right text-gray-500 dark:text-gray-400">
        <thead class="text-xs text-gray-700 uppercase bg-gray-50 dark:bg-gray-700 dark:text-gray-400">
            <tr>
                <th scope="col" class="px-6 py-4">Id</th>
                <th scope="col" class="px-6 py-4">Name</th>
                <th scope="col" class="px-6 py-4">Email</th>
                <th scope="col" class="px-6 py-4">User Type</th>
                <th scope="col" class="px-6 py-4">First Name</th>
                <th scope="col" class="px-6 py-4">Last Name</th>
                <th scope="col" class="px-6 py-4">Middle Name</th>
                <th scope="col" class="px-6 py-4">Date of Birth</th>
                <th scope="col" class="px-6 py-4">Address</th>
                <th scope="col" class="px-6 py-4">Gender</th>
                <th scope="col" class="px-6 py-4">Contact No.</th>
                <th scope="col" class="px-6 py-4">Action</th>
            </tr>
        </thead>
        <tbody id="usersTableBody">
            @foreach ($admins as $user)
            <tr class="user-row bg-white border-b dark:bg-gray-800 dark:border-gray-700">
                <th scope="row" class="px-6 py-4 font-medium text-gray-900 whitespace-nowrap dark:text-white">
                    {{ $user->id }}
                </th>
                <td scope="row" class="px-6 py-4 font-medium text-gray-900 whitespace-nowrap dark:text-white user-name">
                    {{ $user->name }}
                </td>
                <td scope="row" class="px-6 py-4 font-medium text-gray-900 whitespace-nowrap dark:text-white user-email">
                    {{ $user->email }}
                </td>
                <td scope="row" class="px-6 py-4 font-medium text-gray-900 whitespace-nowrap dark:text-white">
                    {{ $user->roleLabel() }}
                </td>
                <td scope="row" class="px-6 py-4 font-medium text-gray-900 whitespace-nowrap dark:text-white">
                    {{ $user->f_name }}
                </td>
                <td scope="row" class="px-6 py-4 font-medium text-gray-900 whitespace-nowrap dark:text-white">
                    {{ $user->l_name }}
                </td>
                <td scope="row" class="px-6 py-4 font-medium text-gray-900 whitespace-nowrap dark:text-white">
                    {{ $user->m_name }}
                </td>
                <td scope="row" class="px-6 py-4 font-medium text-gray-900 whitespace-nowrap dark:text-white">
                    {{ $user->dob }}
                </td>
                <td scope="row" class="px-6 py-4 font-medium text-gray-900 whitespace-nowrap dark:text-white">
                    {{ $user->address }}
                </td>
                <td scope="row" class="px-6 py-4 font-medium text-gray-900 whitespace-nowrap dark:text-white">
                    {{ $user->gender }}
                </td>
                <td scope="row" class="px-6 py-4 font-medium text-gray-900 whitespace-nowrap dark:text-white">
                    {{ $user->contact_no }}
                </td>
                <td class="flex items-center px-6 py-4">
                    <a href="{{ route('admin.users.editWithType', ['user' => $user->id, 'type' => $user->user_type]) }}" class="font-medium text-blue-600 dark:text-blue-500 hover:underline">Edit</a>
                    <button type="button" disabled class="font-medium text-gray-400 dark:text-gray-500 ms-3 cursor-not-allowed" aria-disabled="true">Remove</button>
                </td>
            </tr>
            @endforeach
        </tbody>
    </table>
    <div class="mt-4">{{ $admins->links() }}</div>
</div>



@endsection
