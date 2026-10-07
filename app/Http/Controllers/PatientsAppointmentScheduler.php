<?php

namespace App\Http\Controllers;

use App\Models\Patients;
use App\Models\Appointment;
use App\Models\Doctors;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class PatientsAppointmentScheduler extends Controller
{
    /**
     * Show form to schedule doctor appointment
     */
    public function scheduleAppointment(Request $request)
    {
        $doctors = Doctors::with('user')->get();

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
        $selectedDoctorId = $request->get('doctor_id', old('doctor_id', ''));
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

        return view('patients.schedule-appointment', compact('doctors', 'timeSlots', 'timeSlotAvailability', 'selectedDate', 'selectedDoctorId'));
    }

    /**
     * Store new appointment
     */
    public function storeAppointment(Request $request)
    {
        $request->validate([
            'doctor_id' => 'required|exists:doctors,id',
            'date' => 'required|date|after:today',
            'time' => 'required',
            'reason' => 'nullable|string|max:500'
        ]);

        $user = Auth::user();
        $patient = Patients::where('user_id', $user->id)->first();

        if (!$patient) {
            return redirect()->route('patients.profile.create')
                ->with('error', 'Please complete your patient profile first.');
        }

        // Check if appointment slot is available
        $existingAppointment = Appointment::where('doc_id', $request->doctor_id)
            ->where('date', $request->date)
            ->where('time', $request->time)
            ->where('status', '!=', Appointment::STATUS_CANCELLED)
            ->first();

        if ($existingAppointment) {
            return back()->with('error', 'This time slot is already booked. Please choose another time.');
        }

        Appointment::create([
            'patient_id' => $patient->id,
            'doc_id' => $request->doctor_id,
            'date' => $request->date,
            'time' => $request->time,
            'status' => Appointment::STATUS_PENDING,
            'reason' => $request->reason
        ]);

        return redirect()->route('patients.appointments')
            ->with('success', 'Appointment scheduled successfully! You will receive confirmation soon.');
    }

    /**
     * Display patient's appointments
     */
    public function appointments()
    {
        $user = Auth::user();
        $patient = Patients::where('user_id', $user->id)->first();

        if (!$patient) {
            return redirect()->route('patients.profile.create');
        }

        $appointments = Appointment::where('patient_id', $patient->id)
            ->with('doctor.user')
            ->orderBy('date', 'desc')
            ->get();

        return view('patients.appointments', compact('appointments'));
    }

    /**
     * Cancel an appointment
     */
    public function cancelAppointment(Request $request, $appointmentId)
    {
        $user = Auth::user();
        $patient = Patients::where('user_id', $user->id)->first();

        if (!$patient) {
            return redirect()->route('patients.profile.create');
        }

        $appointment = Appointment::where('id', $appointmentId)
            ->where('patient_id', $patient->id)
            ->first();

        if (!$appointment) {
            return back()->with('error', 'Appointment not found.');
        }

        if ($appointment->status === Appointment::STATUS_CANCELLED) {
            return back()->with('error', 'Cannot cancel already cancelled appointments.');
        }

        $appointment->update(['status' => Appointment::STATUS_CANCELLED]);

        return back()->with('success', 'Appointment cancelled successfully.');
    }
}
