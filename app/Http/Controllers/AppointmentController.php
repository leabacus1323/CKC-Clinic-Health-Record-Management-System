<?php

namespace App\Http\Controllers;

use App\Models\Appointment;
use App\Models\Doctors;
use App\Models\Patients;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

class AppointmentController extends Controller
{
    /**
     * Display a listing of appointments.
     */
    public function index()
    {
        $appointments = Appointment::query()
            ->with([
                'patient:id,user_id',
                'patient.user:id,name',
                'doctor:id,user_id',
                'doctor.user:id,name',
            ])
            ->latest('date')
            ->latest('time')
            ->paginate(25);

        $patients = Patients::query()
            ->with('user:id,name')
            ->select(['id', 'user_id'])
            ->orderBy('id')
            ->get();

        $doctors = Doctors::query()
            ->with('user:id,name')
            ->select(['id', 'user_id'])
            ->orderBy('id')
            ->get();

        return view('appointments.index', compact('appointments', 'patients', 'doctors'));
    }

    /**
     * Show the standalone appointment creation page.
     */
    public function create()
    {
        $patients = Patients::query()
            ->with('user:id,name')
            ->select(['id', 'user_id'])
            ->orderBy('id')
            ->get();

        $doctors = Doctors::query()
            ->with('user:id,name')
            ->select(['id', 'user_id'])
            ->orderBy('id')
            ->get();

        return view('appointments.create', [
            'patients' => $patients,
            'doctors' => $doctors,
            'standalone' => true,
        ]);
    }

    /**
     * Store a newly created appointment.
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'patient_id' => ['required', 'integer', 'exists:patients,id'],
            'date' => ['required', 'date'],
            'time' => ['required', 'date_format:H:i'],
            'doc_id' => ['required', 'integer', 'exists:doctors,id'],
            'reason' => ['nullable', 'string', 'max:1000'],
        ]);

        Appointment::create([
            ...$validated,
            'status' => Appointment::STATUS_PENDING,
        ]);

        return redirect()->route('appointments.index')
            ->with('success', 'Appointment created successfully!');
    }

    /**
     * Display the specified appointment.
     */
    public function show(Appointment $appointment)
    {
        $appointment->load([
            'patient:id,user_id',
                'patient.user:id,name',
            'doctor:id,user_id',
            'doctor.user:id,name',
        ]);

        return view('appointments.show', [
            'appointments' => collect([$appointment]),
            'standalone' => true,
        ]);
    }

    /**
     * Show the form for editing the specified appointment.
     */
    public function edit($id)
    {
        $appointment = Appointment::findOrFail($id);

        return view('appointments.edit', compact('appointment'));
    }

    /**
     * Update the specified appointment.
     */
    public function update(Request $request, $id)
    {
        $validated = $request->validate([
            'patient_id' => ['required', 'integer', 'exists:patients,id'],
            'date' => ['required', 'date'],
            'time' => ['required', 'date_format:H:i'],
            'doc_id' => ['required', 'integer', 'exists:doctors,id'],
            'status' => [
                'required',
                Rule::in([
                    Appointment::STATUS_PENDING,
                    Appointment::STATUS_CONFIRMED,
                    Appointment::STATUS_CANCELLED,
                ]),
            ],
            'reason' => ['nullable', 'string', 'max:1000'],
        ]);

        $appointment = Appointment::findOrFail($id);
        $appointment->update($validated);

        return redirect()->route('appointments.index')
            ->with('success', 'Appointment updated successfully.');
    }

    /**
     * Remove the specified appointment.
     */
    public function destroy(Appointment $appointment)
    {
        $appointment->delete();

        return redirect()->route('appointments.index')
            ->with('success', 'Appointment deleted successfully.');
    }
}
