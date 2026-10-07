<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\DentalExamination;

class AdminDentalExamination extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        //
        $dentals = DentalExamination::with(['patient.user', 'doctor.user'])->get();
        return view('admin.dental.index', compact('dentals'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        //
        return view('admin.dental.create');
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
        $exam = DentalExamination::findOrFail($id);
        return view('admin.dental.edit', compact('exam'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        //
        $exam = DentalExamination::findOrFail($id);

        $validated = $request->validate([
            'diagnosis' => 'nullable|string',
            'teeth_status' => 'nullable|array',
        ]);

        $exam->update($validated);

        return redirect()->route('admin.dental.index')->with('success', 'Dental examination updated successfully!');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        //
    }
}
