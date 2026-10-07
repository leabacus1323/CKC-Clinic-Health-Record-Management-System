<?php

namespace App\Http\Controllers;

use App\Models\ImmunizationRequirement;
use App\Models\User;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;
use Illuminate\View\View;

class ImmunizationRequirementController extends Controller
{
    public function index(Request $request): View
    {
        abort_unless($request->user()->user_type === User::TYPE_ADMIN, 403);
        $requirements = ImmunizationRequirement::query()->orderBy('registry_type')->orderBy('name')->get();

        return view('immunizations.requirements', compact('requirements'));
    }

    public function store(Request $request): RedirectResponse
    {
        abort_unless($request->user()->user_type === User::TYPE_ADMIN, 403);
        $data = $this->validated($request);
        ImmunizationRequirement::create($data);

        return back()->with('success', 'Immunization requirement created.');
    }

    public function update(Request $request, ImmunizationRequirement $requirement): RedirectResponse
    {
        abort_unless($request->user()->user_type === User::TYPE_ADMIN, 403);
        $requirement->update($this->validated($request, $requirement));

        return back()->with('success', 'Immunization requirement updated.');
    }

    private function validated(Request $request, ?ImmunizationRequirement $requirement = null): array
    {
        return $request->validate([
            'code' => ['required', 'string', 'max:50', Rule::unique('immunization_requirements', 'code')->ignore($requirement?->id)],
            'name' => ['required', 'string', 'max:255'],
            'description' => ['nullable', 'string', 'max:2000'],
            'registry_type' => ['required', Rule::in(['student', 'non_student', 'all'])],
            'minimum_doses' => ['required', 'integer', 'min:1', 'max:20'],
            'due_age_months' => ['nullable', 'integer', 'min:0', 'max:1200'],
            'booster_interval_months' => ['nullable', 'integer', 'min:1', 'max:1200'],
            'required_by_date' => ['nullable', 'date'],
            'is_active' => ['nullable', 'boolean'],
        ]) + ['is_active' => $request->boolean('is_active')];
    }
}
