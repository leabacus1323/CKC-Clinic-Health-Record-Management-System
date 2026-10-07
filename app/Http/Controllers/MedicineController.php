<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Medicine;
class MedicineController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
      
        $medicine = Medicine::all();
        return view('medicine.index', compact('medicine'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        return view('medicine.create');
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'description' => 'required|string|max:255',
            'quantity' => 'required',
            'expiration_date' => 'required',
            'medicine_type' => 'required',
        ]);

        Medicine::create([
            'name' => $request->name,
            'description' => $request->description,
            'quantity' => $request->quantity,
            'expiration_date' => $request->expiration_date,
            'medicine_type' => $request->medicine_type,
        ]);

        return redirect('/medicine')->with('status','Category Created Successfully');
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        $medicine = Medicine::findOrFail($id); 
        return view('medicine.show', compact('medicine')); 
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(string $id)
    {
        $medicine = Medicine::findOrFail($id); 
        return view('medicine.edit', compact('medicine')); 
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'description' => 'required|string|max:255',
            'quantity' => 'required',
            'expiration_date' => 'required',
            'medicine_type' => 'required',
        ]);
        $medicine = Medicine::findOrFail($id); // Fetch the medicine instance
        $medicine->update([
            'name' => $request->name,
            'description' => $request->description,
            'quantity' => $request->quantity,
            'expiration_date' => $request->expiration_date,
            'medicine_type' => $request->medicine_type,
        ]);

        return redirect('/medicine')->with('status','Medicine Updated Successfully');

    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        $medicine = Medicine::findOrFail($id); 
        $medicine->delete();
        return redirect('/medicine')->with('status','Medicine Deleted Successfully');
    }
}
