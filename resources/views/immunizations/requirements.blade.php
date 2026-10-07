@extends('admin.layout.navigation')
@section('content')
<div class="max-w-7xl mx-auto space-y-6">
    <div><h1 class="text-2xl font-bold text-gray-900 dark:text-white">Immunization Requirements</h1><p class="text-sm text-gray-600 dark:text-gray-300">Configure school rules; no vaccine is treated as mandatory unless it is activated here.</p></div>
    @if(session('success')) <div class="rounded-lg bg-green-50 p-4 text-green-800">{{ session('success') }}</div> @endif
    @if($errors->any()) <div class="rounded-lg bg-red-50 p-4 text-red-800"><ul class="list-disc pl-5">@foreach($errors->all() as $error)<li>{{ $error }}</li>@endforeach</ul></div> @endif

    <section class="rounded-xl border border-gray-200 bg-white p-6 shadow-sm">
        <h2 class="font-semibold text-gray-900">Add requirement</h2>
        <form method="POST" action="{{ route('immunization-requirements.store') }}" class="mt-4 grid gap-4 md:grid-cols-2 xl:grid-cols-4">
            @csrf
            <input name="code" required placeholder="Code" class="rounded-lg border-gray-300"><input name="name" required placeholder="Vaccine/requirement name" class="rounded-lg border-gray-300">
            <select name="registry_type" required class="rounded-lg border-gray-300"><option value="student">Student</option><option value="non_student">Non-student</option><option value="all">All registries</option></select>
            <input name="minimum_doses" type="number" min="1" max="20" value="1" required class="rounded-lg border-gray-300" placeholder="Minimum doses">
            <input name="due_age_months" type="number" min="0" placeholder="Due age in months" class="rounded-lg border-gray-300"><input name="booster_interval_months" type="number" min="1" placeholder="Booster interval months" class="rounded-lg border-gray-300">
            <input name="required_by_date" type="date" class="rounded-lg border-gray-300"><label class="flex items-center gap-2 text-sm"><input type="checkbox" name="is_active" value="1" checked> Active</label>
            <textarea name="description" rows="2" placeholder="Description" class="md:col-span-2 xl:col-span-3 rounded-lg border-gray-300"></textarea><button class="rounded-lg bg-blue-700 px-5 py-2.5 text-sm font-semibold text-white">Create</button>
        </form>
    </section>

    <section class="rounded-xl border border-gray-200 bg-white p-6 shadow-sm">
        <div class="space-y-4">
            @forelse($requirements as $requirement)
                <form method="POST" action="{{ route('immunization-requirements.update', $requirement) }}" class="grid gap-3 rounded-lg border border-gray-200 p-4 md:grid-cols-2 xl:grid-cols-5">
                    @csrf @method('PUT')
                    <input name="code" value="{{ $requirement->code }}" required class="rounded-lg border-gray-300"><input name="name" value="{{ $requirement->name }}" required class="rounded-lg border-gray-300">
                    <select name="registry_type" class="rounded-lg border-gray-300">@foreach(['student'=>'Student','non_student'=>'Non-student','all'=>'All'] as $key=>$label)<option value="{{ $key }}" @selected($requirement->registry_type===$key)>{{ $label }}</option>@endforeach</select>
                    <input name="minimum_doses" type="number" min="1" value="{{ $requirement->minimum_doses }}" class="rounded-lg border-gray-300"><label class="flex items-center gap-2 text-sm"><input type="checkbox" name="is_active" value="1" @checked($requirement->is_active)> Active</label>
                    <input name="due_age_months" type="number" value="{{ $requirement->due_age_months }}" placeholder="Due age months" class="rounded-lg border-gray-300"><input name="booster_interval_months" type="number" value="{{ $requirement->booster_interval_months }}" placeholder="Booster months" class="rounded-lg border-gray-300"><input name="required_by_date" type="date" value="{{ optional($requirement->required_by_date)->format('Y-m-d') }}" class="rounded-lg border-gray-300"><textarea name="description" class="rounded-lg border-gray-300">{{ $requirement->description }}</textarea><button class="rounded-lg bg-gray-900 px-4 py-2 text-sm font-semibold text-white">Save</button>
                </form>
            @empty <p class="text-sm text-gray-500">No requirements configured.</p> @endforelse
        </div>
    </section>
</div>
@endsection
