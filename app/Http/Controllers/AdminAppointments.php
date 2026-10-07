<?php

namespace App\Http\Controllers;

use App\Models\Appointment;
use Illuminate\Support\Facades\DB;
use Illuminate\Http\Request;

class AdminAppointments extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        //
        $query = Appointment::with(['patient.user', 'doctor.user']);

        // Handle search functionality
        if ($request->has('search') && !empty($request->search)) {
            $searchTerm = $request->search;
            $query->where(function($q) use ($searchTerm) {
                $q->whereHas('patient.user', function($patientQuery) use ($searchTerm) {
                    $patientQuery->where('name', 'LIKE', '%' . $searchTerm . '%');
                })
                ->orWhereHas('doctor.user', function($doctorQuery) use ($searchTerm) {
                    $doctorQuery->where('name', 'LIKE', '%' . $searchTerm . '%');
                })
                ->orWhereHas('patient', function($patientTypeQuery) use ($searchTerm) {
                    $patientTypeQuery->where('patient_type', 'LIKE', '%' . $searchTerm . '%');
                })
                ->orWhereHas('doctor', function($doctorSpecQuery) use ($searchTerm) {
                    $doctorSpecQuery->where('specialization', 'LIKE', '%' . $searchTerm . '%');
                })
                ->orWhere('status', 'LIKE', '%' . $searchTerm . '%')
                ->orWhere('date', 'LIKE', '%' . $searchTerm . '%')
                ->orWhere('reason', 'LIKE', '%' . $searchTerm . '%');
            });
        }

        $appointments = $query->orderBy('date', 'desc')
            ->orderBy('time', 'desc')
            ->get();

        // Calculate statistics (for all appointments, not just filtered)
        $allAppointments = Appointment::all();
        $stats = [
            'total' => $allAppointments->count(),
            'pending' => $allAppointments->where('status', Appointment::STATUS_PENDING)->count(),
            'confirmed' => $allAppointments->where('status', Appointment::STATUS_CONFIRMED)->count(),
            'cancelled' => $allAppointments->where('status', Appointment::STATUS_CANCELLED)->count(),
            'today' => $allAppointments->where('date', now()->format('Y-m-d'))->count(),
        ];

        return view('admin.appointments.index', compact('appointments', 'stats'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create(Request $request)
    {
        //
        // Get patients with their user information, excluding doctors (user_type = 3)
        $patients = \App\Models\Patients::join('users', 'users.id', '=', 'patients.user_id')
            ->whereIn('users.user_type', [1, 2]) // Only students and staff
            ->select('patients.id', 'users.name', 'patients.patient_type')
            ->get();

        // Get doctors with their user information
        $doctors = \App\Models\Doctors::join('users', 'users.id', '=', 'doctors.user_id')
            ->select('doctors.id', 'users.name', 'doctors.specialization')
            ->get();

        // Define available time slots (8 AM to 4 PM)
        $timeSlots = [
            '08:00' => '8:00 AM',
            '09:00' => '9:00 AM',
            '10:00' => '10:00 AM',
            '11:00' => '11:00 AM',
            '13:00' => '1:00 PM',
            '14:00' => '2:00 PM',
            '15:00' => '3:00 PM',
            '16:00' => '4:00 PM'
        ];

        $selectedDate = $request->get('date', old('date'));
        $selectedDoctorId = $request->get('doc_id', old('doc_id', ''));
        $timeSlotAvailability = [];

        // If date and doctor are selected, check availability
        if ($selectedDate && $selectedDoctorId) {
            foreach ($timeSlots as $time => $label) {
                $isBooked = Appointment::where('doc_id', $selectedDoctorId)
                    ->where('date', $selectedDate)
                    ->where('time', $time)
                    ->where('status', '!=', Appointment::STATUS_CANCELLED)
                    ->exists();

                $timeSlotAvailability[$time] = !$isBooked;
            }
        }

        return view('admin.appointments.create', compact('patients', 'doctors', 'timeSlots', 'timeSlotAvailability', 'selectedDate', 'selectedDoctorId'));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        // Validate the incoming request data
        $request->validate([
            'patient_id' => 'required', // Ensure the patient ID exists in the patients table
            'date' => 'required|date',
            'time' => 'required|date_format:H:i', // Time should be in HH:MM format
            'doc_id' => 'required', // Ensure the doctor ID exists in the doctors table
        ]);
        // Create a new appointment
        Appointment::create([
            'patient_id' => $request->patient_id,
            'date' => $request->date,
            'time' => $request->time,
            'doc_id' => $request->doc_id,
            'status' => Appointment::STATUS_PENDING,
        ]);

        // Redirect back to appointments index with a success message
        return redirect()->route('admin.appointments.create')->with('success', 'Appointment created successfully!');
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
    public function edit(Request $request, string $id)
    {
        //
        // Find the appointment by ID
        $appointment = Appointment::findOrFail($id);

        // Get patients with their user information, excluding doctors (user_type = 3)
        $patients = \App\Models\Patients::join('users', 'users.id', '=', 'patients.user_id')
            ->whereIn('users.user_type', [1, 2]) // Only students and staff
            ->select('patients.id', 'users.name', 'patients.patient_type')
            ->get();

        // Get doctors with their user information
        $doctors = \App\Models\Doctors::join('users', 'users.id', '=', 'doctors.user_id')
            ->select('doctors.id', 'users.name', 'doctors.specialization')
            ->get();

        // Define available time slots (8 AM to 4 PM)
        $timeSlots = [
            '08:00' => '8:00 AM',
            '09:00' => '9:00 AM',
            '10:00' => '10:00 AM',
            '11:00' => '11:00 AM',
            '13:00' => '1:00 PM',
            '14:00' => '2:00 PM',
            '15:00' => '3:00 PM',
            '16:00' => '4:00 PM'
        ];

        $selectedDate = $request->get('date', old('date', $appointment->date));
        $selectedDoctorId = $request->get('doc_id', old('doc_id', $appointment->doc_id));
        $timeSlotAvailability = [];

        // If date and doctor are selected, check availability
        if ($selectedDate && $selectedDoctorId) {
            foreach ($timeSlots as $time => $label) {
                $isBooked = Appointment::where('doc_id', $selectedDoctorId)
                    ->where('date', $selectedDate)
                    ->where('time', $time)
                    ->where('status', '!=', Appointment::STATUS_CANCELLED)
                    ->where('id', '!=', $id) // Exclude current appointment from availability check
                    ->exists();

                $timeSlotAvailability[$time] = !$isBooked;
            }
        }

        return view('admin.appointments.edit', compact('appointment', 'patients', 'doctors', 'timeSlots', 'timeSlotAvailability', 'selectedDate', 'selectedDoctorId'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, $id)
    {
        // Validate the request data
        $request->validate([
            'patient_id' => 'required',
            'date' => 'required|date',
            'time' => 'required|date_format:H:i',
            'doc_id' => 'required',
            'status' => 'required',
        ]);

        // Find the appointment by ID
        $appointment = Appointment::findOrFail($id);

        // Update the appointment details
        $appointment->patient_id = $request->input('patient_id');
        $appointment->date = $request->input('date');
        $appointment->time = $request->input('time');
        $appointment->doc_id = $request->input('doc_id');
        $appointment->status = $request->input('status');

        // Save the updated appointment
        $appointment->save();

        // Redirect back with a success message
        return redirect()->route('admin.appointments.index')->with('success', 'Appointment updated successfully.');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Appointment $appointment)
    {
        //
        $appointment->delete();

        return redirect()->route('admin.appointments.index')
                         ->with('success', 'Appointment deleted successfully.');
    }
}
