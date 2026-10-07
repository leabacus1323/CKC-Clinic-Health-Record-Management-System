<?php

namespace App\Http\Controllers;

use App\Models\Doctors;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;

class DoctorProfileController extends Controller
{
    /**
     * Show doctor profile view
     */
    public function show()
    {
        $user = Auth::user();
        $doctor = Doctors::where('user_id', $user->id)->first();

        if (!$doctor) {
            $doctor = Doctors::firstOrNew(
                ['user_id' => $user->id],
                [
                    'specialization' => '',
                    'address' => '',
                    'is_available' => false,
                ]
            );

            return view('doctor.edit-profile', compact('user', 'doctor'));
        }

        return view('doctor.profile', compact('user', 'doctor'));
    }

    /**
     * Show form for editing doctor profile
     */
    public function edit()
    {
        $user = Auth::user();
        $doctor = Doctors::firstOrCreate(
            ['user_id' => $user->id],
            [
                'specialization' => '',
                'address' => '',
                'is_available' => false,
            ]
        );

        return view('doctor.edit-profile', compact('user', 'doctor'));
    }

    /**
     * Update doctor profile and user information
     */
    public function update(Request $request)
    {
        if ($request->input('specialization') !== 'Other') {
            $request->merge(['specialization_custom' => null]);
        }

        $validator = Validator::make($request->all(), [
            'name' => 'required|string|max:255',
            'email' => 'required|string|email|max:255|unique:users,email,' . Auth::id(),
            'specialization' => 'required|string|max:255',
            'specialization_custom' => 'nullable|string|max:255',
            'address' => 'required|string|max:255',
            'is_available' => 'nullable|boolean',
        ]);

        $validator->sometimes('specialization_custom', ['required', 'string', 'max:255'], function ($input) {
            return $input->specialization === 'Other';
        });

        $validator->validate();

        $user = Auth::user();

        $specialization = $request->specialization === 'Other'
            ? $request->specialization_custom
            : $request->specialization;

        // Ensure a Doctors record exists for this user before updating
        $doctor = Doctors::updateOrCreate(
            ['user_id' => $user->id],
            [
                'specialization' => $specialization,
                'address' => $request->address,
                'is_available' => $request->has('is_available') ? true : false,
            ]
        );

        // Update user information
        $user->update([
            'name' => $request->name,
            'email' => $request->email,
        ]);

        return redirect()->route('doctor.profile.show')
            ->with('success', 'Profile updated successfully!');
    }

    /**
     * Update doctor password
     */
    public function updatePassword(Request $request)
    {
        $request->validate([
            'current_password' => 'required',
            'new_password' => 'required|min:8|confirmed',
        ]);

        $user = Auth::user();

        if (!Hash::check($request->current_password, $user->password)) {
            return back()->with('error', 'Current password is incorrect.');
        }

        $user->update([
            'password' => Hash::make($request->new_password),
        ]);

        return back()->with('success', 'Password updated successfully!');
    }
}
