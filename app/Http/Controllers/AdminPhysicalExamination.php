<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\PhysicalExamination;

class AdminPhysicalExamination extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        //
        $physicals = PhysicalExamination::with(['patient.user', 'doctor.user'])->get();
        return view('admin.physical.index', compact('physicals'));
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
        $exam = PhysicalExamination::findOrFail($id);
        return view('admin.physical.edit', compact('exam'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        //
        $exam = PhysicalExamination::findOrFail($id);
        
        $validated = $request->validate([
            'height' => 'nullable|string',
            'weight' => 'nullable|string',
            'bp' => 'nullable|string',
            'heart' => 'nullable|string',
            'lungs' => 'nullable|string',
            'eyes' => 'nullable|string',
            'ears' => 'nullable|string',
            'nose' => 'nullable|string',
            'throat' => 'nullable|string',
            'skin' => 'nullable|string',
            'remarks' => 'nullable|string',
        ]);
        
        $exam->update($validated);
        
        return redirect()->route('admin.physical.index')->with('success', 'Physical examination updated successfully!');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        //
    }
}
