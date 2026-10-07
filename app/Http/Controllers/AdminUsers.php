<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Validation\Rules;
use Illuminate\Validation\Rule;
use App\Models\User;
use Illuminate\Support\Facades\Hash;
use Illuminate\Database\QueryException;

class AdminUsers extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $search = trim((string) $request->query('search', ''));

        $admins = User::query()
            ->select([
                'id', 'name', 'email', 'user_type', 'f_name', 'm_name', 'l_name',
                'dob', 'address', 'gender', 'contact_no',
            ])
            ->when($search !== '', function ($query) use ($search) {
                $query->where(function ($searchQuery) use ($search) {
                    $searchQuery->where('name', 'like', "%{$search}%")
                        ->orWhere('email', 'like', "%{$search}%");
                });
            })
            ->orderByDesc('id')
            ->paginate(25)
            ->withQueryString();

        return view('admin.users.index', compact('admins', 'search'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        //
        return view('admin.users.create');
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        // Validate the incoming request data
        $request->validate([
            'name' => 'required',
            'user_type' => ['required', 'integer', Rule::in([
                User::TYPE_ADMIN, User::TYPE_STUDENT, User::TYPE_STAFF, User::TYPE_DOCTOR,
                User::TYPE_FACULTY, User::TYPE_GUARDIAN, User::TYPE_PRINCIPAL, User::TYPE_TEACHER,
            ])],
            'f_name' => 'required',
            'm_name' => 'required',
            'l_name' => 'required',
            'dob' => 'required',
            'address' => 'required',
            'gender' => 'required',
            'contact_no' => 'required',
            'email' => ['required', 'email', 'max:255', 'unique:users,email'],
            'password' => ['required', 'confirmed', Rules\Password::defaults()],
        ]);

        // Create regular user account for all user types (Student, Faculty & Staff, Doctor)
        User::create([
            'name' => $request->name,
            'user_type' => $request->user_type,
            'f_name' => $request->f_name,
            'm_name' => $request->m_name,
            'l_name' => $request->l_name,
            'dob' => $request->dob,
            'address' => $request->address,
            'gender' => $request->gender,
            'contact_no' => $request->contact_no,
            'email' => $request->email,
            'password' => Hash::make($request->password),
        ]);

        // Redirect
        return redirect()->route('admin.users.create')->with('success','User created successfully!');
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        $user = User::findOrFail($id);

        return redirect()->route('admin.users.editWithType', [
            'user' => $user->id,
            'type' => $user->user_type,
        ]);
    }

    /**
     * Show the form for editing the 2 argument of specified resource.
     */
    public function edit(Request $request, $id, $type = null)
    {
        // Always use the users table for editing, even for admin-type users.
        $admin = User::findOrFail($id);

        return view('admin.users.edit', compact('admin'));
    }

    /**
     * Remove the 2 argument specified resource from storage.
     */
    public function update(Request $request, $id, $type = null)
    {
        $admin = User::findOrFail($id);

        // Validation rules - password is optional for update
        $rules = [
            'name' => 'required|string|max:255',
            'user_type' => ['required', 'integer', Rule::in([
                User::TYPE_ADMIN, User::TYPE_STUDENT, User::TYPE_STAFF, User::TYPE_DOCTOR,
                User::TYPE_FACULTY, User::TYPE_GUARDIAN, User::TYPE_PRINCIPAL, User::TYPE_TEACHER,
            ])],
            'f_name' => 'required|string|max:255',
            'm_name' => 'nullable|string|max:255',
            'l_name' => 'required|string|max:255',
            'dob' => 'required|date',
            'address' => 'nullable|string',
            'gender' => 'required|string|max:1',
            'contact_no' => 'nullable|string|max:20',
            'email' => 'required|email|unique:users,email,' . $id,
        ];

        // Only validate password if it's being changed
        if ($request->filled('password')) {
            $rules['password'] = ['confirmed', Rules\Password::defaults()];
        }

        $request->validate($rules);

        // Update user fields
        $admin->name = $request->name;
        $admin->user_type = $request->user_type;
        $admin->f_name = $request->f_name;
        $admin->m_name = $request->m_name;
        $admin->l_name = $request->l_name;
        $admin->dob = $request->dob;
        $admin->address = $request->address;
        $admin->gender = $request->gender;
        $admin->contact_no = $request->contact_no;
        $admin->email = $request->email;

        // Only update password if provided
        if ($request->filled('password')) {
            $admin->password = Hash::make($request->password);
        }

        $admin->save();

        // Redirect with the NEW user_type to ensure the URL reflects the updated type
        return redirect()->route('admin.users.editWithType', ['user' => $admin->id, 'type' => $admin->user_type])->with('success', 'User updated successfully!');
    }

    /**
     * Remove the specified user from storage.
     */
    public function destroy(Request $request, string $id)
    {
        return $this->deleteUser($request, $id);
    }

    /**
     * Remove the 2 argument specified resource from storage.
     */
    public function deleteWithType(Request $request, $id, $type)
    {
        return $this->deleteUser($request, (string) $id);
    }

    private function deleteUser(Request $request, string $id)
    {
        $user = User::findOrFail($id);

        if ((int) $request->user()?->id === (int) $user->id) {
            return $this->deletionConflict($request, 'You cannot delete the account that is currently signed in.');
        }

        try {
            $user->delete();
        } catch (QueryException $exception) {
            report($exception);

            return $this->deletionConflict(
                $request,
                'This account is linked to protected operational or health records and cannot be deleted. Reassign or archive its related records first.'
            );
        }

        return redirect()->route('admin.users.index')->with('success', 'User deleted successfully');
    }

    private function deletionConflict(Request $request, string $message)
    {
        if ($request->expectsJson()) {
            return response()->json(['message' => $message], 409);
        }

        return redirect()->route('admin.users.index')->with('error', $message);
    }
}
