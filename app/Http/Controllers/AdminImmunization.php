<?php

namespace App\Http\Controllers;
use App\Models\ImmunizationRecords;
use Illuminate\Http\Request;

class AdminImmunization extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        //
        $immunizations = ImmunizationRecords::with(['patient.user'])->get();
        return view('admin.immunization.index', compact('immunizations'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        //
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(string $id)
    {
        //
        $record = ImmunizationRecords::findOrFail($id);
        return view('admin.immunization.edit', compact('record'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        //
        $record = ImmunizationRecords::findOrFail($id);

        $validated = $request->validate([
            'vaccine_name' => 'required|string',
            'vaccine_type' => 'nullable|string',
            'administered_by' => 'nullable|string',
            'dosage' => 'nullable|string',
            'site_of_administration' => 'nullable|string',
            'expiration_date' => 'nullable|date',
            'notes' => 'nullable|string',
        ]);

        $record->update($validated);

        return redirect()->route('admin.immunization.index')->with('success', 'Immunization record updated successfully!');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        //
    }
}
