<?php

namespace App\Http\Controllers;

use App\Models\Patients;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class ClinicalLifeSafetyController extends Controller
{
    public function index(Request $request): View
    {
        $search = trim((string) $request->query('search', ''));

        $patients = Patients::query()
            ->select([
                'id', 'user_id', 'school_id', 'registry_type', 'life_safety_visible_to_teachers',
                'life_safety_alert', 'emergency_medication', 'life_safety_reviewed_at',
            ])
            ->with('user:id,name')
            ->when($search !== '', function ($query) use ($search) {
                $query->where(function ($nested) use ($search) {
                    $nested->where('school_id', 'like', "%{$search}%")
                        ->orWhereHas('user', fn ($userQuery) => $userQuery->where('name', 'like', "%{$search}%"));
                });
            })
            ->orderByDesc('life_safety_visible_to_teachers')
            ->orderBy('school_id')
            ->paginate(25)
            ->withQueryString();

        return view('clinical.life-safety.index', compact('patients', 'search'));
    }

    public function edit(Patients $patient): View
    {
        $patient->loadMissing('user:id,name');

        return view('clinical.life-safety.edit', compact('patient'));
    }

    public function update(Request $request, Patients $patient): RedirectResponse
    {
        $data = $request->validate([
            'life_safety_visible_to_teachers' => ['nullable', 'boolean'],
            'life_safety_alert' => ['nullable', 'string', 'max:500'],
            'emergency_medication' => ['nullable', 'string', 'max:255'],
            'life_safety_instructions' => ['nullable', 'string', 'max:2000'],
        ]);

        $visible = $request->boolean('life_safety_visible_to_teachers');
        if ($visible) {
            $request->validate([
                'life_safety_alert' => ['required_without_all:emergency_medication,life_safety_instructions'],
            ]);
        }

        $patient->update([
            ...$data,
            'life_safety_visible_to_teachers' => $visible,
            'life_safety_reviewed_at' => now(),
            'life_safety_reviewed_by' => $request->user()->id,
        ]);

        return redirect()->route('clinical.life-safety.index')
            ->with('success', 'Teacher life-safety information updated.');
    }
}
