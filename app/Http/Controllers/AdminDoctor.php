<?php

namespace App\Http\Controllers;
use App\Models\Doctors;
use Illuminate\Http\Request;
use App\Models\User;
use App\Models\CertificateRequest;
use Illuminate\Database\QueryException;

class AdminDoctor extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $search = trim((string) $request->query('search', ''));

        $doctors = Doctors::query()
            ->select(['id', 'user_id', 'specialization', 'address', 'is_available'])
            ->with('user:id,f_name,l_name,contact_no,name')
            ->when($search !== '', function ($query) use ($search) {
                $query->where(function ($doctorQuery) use ($search) {
                    $doctorQuery->where('specialization', 'like', "%{$search}%")
                        ->orWhere('address', 'like', "%{$search}%")
                        ->orWhereHas('user', function ($userQuery) use ($search) {
                            $userQuery->where('name', 'like', "%{$search}%")
                                ->orWhere('f_name', 'like', "%{$search}%")
                                ->orWhere('l_name', 'like', "%{$search}%");
                        });
                });
            })
            ->orderByDesc('id')
            ->paginate(25)
            ->withQueryString();

        return view('admin.doctors.index', compact('doctors', 'search'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        // User Type = 3 for Doctors
        $users = User::where('user_type','=','3')
        ->get();
        return view('admin.doctors.create', compact('users'));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        // Validate the incoming request data
        $request->validate([
            'user_id' => 'required|unique:doctors',
            'specialization' => 'required',
            'specialization_custom' => 'required_if:specialization,Other',
            'address' => 'required',
        ]);

        $specialization = $request->specialization === 'Other'
            ? $request->specialization_custom
            : $request->specialization;

        // Create a new doctor
        Doctors::create([
            'user_id' => $request->user_id,
            'specialization' => $specialization,
            'address' => $request->address,
        ]);

        // Redirect back to doctor create with a success message
        return redirect()->route('admin.doctors.create')->with('success', 'Doctor created successfully!');
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
        $doctors = Doctors::findOrFail($id);
        // User Type = 3 for Doctors
        $users = User::where('user_type','=','3')
        ->get();
        //
        return view('admin.doctors.edit', compact('doctors','users'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        //
        $request->validate([
            'user_id' => 'required',
            'specialization' => 'required',
            'specialization_custom' => 'required_if:specialization,Other',
            'address' => 'required',
            'is_available' => 'required',
        ]);

        $specialization = $request->specialization === 'Other'
            ? $request->input('specialization_custom')
            : $request->input('specialization');

        // Find the doctor by ID
        $doctor = Doctors::findOrFail($id);

        // Update the doctor details
        $doctor->user_id = $request->input('user_id');
        $doctor->specialization = $specialization;
        $doctor->address = $request->input('address');
        $doctor->is_available = $request->input('is_available');

        // Save the updated doctor
        $doctor->save();

        // Redirect back with a success message
        return redirect()->route('admin.doctors.index')->with('success','Doctor updated successfully!');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Request $request, Doctors $doctor)
    {
        $hasProtectedRecords = $doctor->appointments()->exists()
            || $doctor->dentalExaminations()->exists()
            || $doctor->physicalExaminations()->exists()
            || $doctor->prescriptionRecords()->exists()
            || CertificateRequest::where('doctor_id', $doctor->id)->exists();

        if ($hasProtectedRecords) {
            return $this->deletionConflict($request, 'This doctor is linked to appointments or clinical records and cannot be deleted. Mark the provider unavailable instead.');
        }

        try {
            $doctor->delete();
        } catch (QueryException $exception) {
            report($exception);
            return $this->deletionConflict($request, 'This doctor is still referenced by protected records and cannot be deleted.');
        }

        return redirect()->route('admin.doctors.index')->with('success', 'Doctor deleted successfully!');
    }

    private function deletionConflict(Request $request, string $message)
    {
        if ($request->expectsJson()) {
            return response()->json(['message' => $message], 409);
        }

        return redirect()->route('admin.doctors.index')->with('error', $message);
    }
}
