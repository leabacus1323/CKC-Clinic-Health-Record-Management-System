<?php

namespace App\Http\Controllers;
use App\Models\Immunization;
use Illuminate\Http\Request;

class ImmunizationController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $immunization = Immunization::all();
        return view('immunization.index', compact('immunization'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        return view('immunization.create', ['standalone' => true]);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'description' => 'required|string|max:255',
        ]);

        Immunization::create([
            'name' => $request->name,
            'description' => $request->description,

        ]);

        return redirect('/immunization')->with('success','Immunization Created Successfully');
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        $immunization = Immunization::findOrFail($id);
        return view('immunization.show', compact('immunization'));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(string $id)
    {
        $immunization = Immunization::findOrFail($id);
        return view('immunization.edit', compact('immunization'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'description' => 'required|string|max:255',

        ]);
        $immunization = Immunization::findOrFail($id); // Fetch the immunization instance
        $immunization->update([
            'name' => $request->name,
            'description' => $request->description,

        ]);

        return redirect('/immunization')->with('status','Immunization Updated Successfully');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        $immunization = Immunization::findOrFail($id);
        $immunization->delete();
        return redirect('/immunization')->with('status','Immunization Deleted Successfully');
    }
}
