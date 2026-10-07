<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Medicine;
use Symfony\Component\HttpFoundation\StreamedResponse;
use Illuminate\Support\Facades\DB;

class AdminMedicines extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        //
        $medicines = Medicine::all();
        return view('admin.medicines.index', compact('medicines'));
    }

    /**
     * Export medicines data to CSV
     */
    public function export()
    {
        $medicines = Medicine::all();

        $csvContent = "ID,Name,Description,Quantity,Expiration Date,Medicine Type,Created At,Updated At\n";

        foreach ($medicines as $medicine) {
            $csvContent .= sprintf(
                "%d,\"%s\",\"%s\",%d,%s,%s,%s,%s\n",
                $medicine->id,
                str_replace('"', '""', $medicine->name ?? ''),
                str_replace('"', '""', $medicine->description ?? ''),
                $medicine->quantity ?? 0,
                $medicine->expiration_date ?? '',
                $medicine->medicine_type ?? '',
                $medicine->created_at ?? '',
                $medicine->updated_at ?? ''
            );
        }

        // Add BOM for Excel UTF-8 compatibility
        $csvContent = "\xEF\xBB\xBF" . $csvContent;

        return response($csvContent, 200, [
            'Content-Type' => 'text/csv; charset=UTF-8',
            'Content-Disposition' => 'attachment; filename="medicines.csv"',
            'Cache-Control' => 'no-cache, private',
            'Pragma' => 'no-cache',
            'Expires' => '0',
        ]);
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        //
        return view('admin.medicines.create');
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        // Validate the incoming request data
        $request->validate([
            'name' => 'required',
            'description' => 'required',
            'quantity' => ['numeric', 'required'],
            'expiration_date' => ['date', 'required'],
            'medicine_type' => 'required',
        ]);

        // Create a new medicine
        Medicine::create([
            'name' => $request->name,
            'description' => $request->description,
            'quantity' => $request->quantity,
            'expiration_date' => $request->expiration_date,
            'medicine_type' => $request->medicine_type,
        ]);

        // Redirect back to medicine index with a success message
        return redirect()->route('admin.medicines.create')->with('success','Medicine created successfully!');
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
        $medicine = Medicine::findOrFail($id);
        //
        return view('admin.medicines.edit', compact('medicine'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        // Validate the request data
        $request->validate([
            'name' => 'required',
            'description' => 'required',
            'quantity' => ['numeric', 'required'],
            'expiration_date' => ['date', 'required'],
            'medicine_type' => 'required',
        ]);

        // Find the medicine by ID
        $medicine = Medicine::findOrFail($id);

        // Update the medicine details
        $medicine->name = $request->input('name');
        $medicine->description = $request->input('description');
        $medicine->quantity = $request->input('quantity');
        $medicine->expiration_date = $request->input('expiration_date');
        $medicine->medicine_type = $request->input('medicine_type');

        // Save the updated medicine
        $medicine->save();

        // Redirect back with a success message
        return redirect()->route('admin.medicines.index')->with('success', 'Medicine updated successfully!');

    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Medicine $medicine)
    {
        //
        $medicine->delete();

        return redirect()->route('admin.medicines.index')->with('success','Medicine deleted successfully!');
    }
}
