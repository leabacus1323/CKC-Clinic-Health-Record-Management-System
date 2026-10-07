<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\User;
use App\Models\Medicine;
use App\Models\Patients;
use Maatwebsite\Excel\Facades\Excel;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\DB;

class ImportController extends Controller
{
    /**
     * Parse various date formats into YYYY-MM-DD
     */
    private function parseDate(string $dateRaw): ?string
    {
        $dateRaw = trim($dateRaw);

        // Already in YYYY-MM-DD format
        if (preg_match('/^\d{4}-\d{2}-\d{2}$/', $dateRaw)) {
            return $dateRaw;
        }

        // Excel serial date (number)
        if (is_numeric($dateRaw)) {
            $unixDate = ($dateRaw - 25569) * 86400;
            return date('Y-m-d', $unixDate);
        }

        // Try common formats
        $formats = [
            'Y-m-d',
            'm/d/Y',
            'd/m/Y',
            'm-d-Y',
            'd-m-Y',
            'Y/m/d',
            'F j, Y',
            'j F Y',
            'Ymd',
        ];

        foreach ($formats as $format) {
            $date = \DateTime::createFromFormat($format, $dateRaw);
            if ($date && $date->format($format) === $dateRaw) {
                return $date->format('Y-m-d');
            }
        }

        // Try strtotime as fallback
        $timestamp = strtotime($dateRaw);
        if ($timestamp !== false) {
            return date('Y-m-d', $timestamp);
        }

        return null;
    }

    /**
     * Split full name into first, middle, and last name
     */
    private function splitName(string $name): array
    {
        $parts = preg_split('/\s+/', trim($name));
        $count = count($parts);

        if ($count === 1) {
            return ['f_name' => $parts[0], 'm_name' => '', 'l_name' => ''];
        } elseif ($count === 2) {
            return ['f_name' => $parts[0], 'm_name' => '', 'l_name' => $parts[1]];
        } else {
            $lName = array_pop($parts);
            $fName = array_shift($parts);
            $mName = implode(' ', $parts);
            return ['f_name' => $fName, 'm_name' => $mName, 'l_name' => $lName];
        }
    }

    /**
     * Show import selection page
     */
    public function index()
    {
        return view('admin.import.index');
    }

    /**
     * Show import form for users
     */
    public function users()
    {
        return view('admin.import.users');
    }

    /**
     * Show import form for medicines
     */
    public function medicines()
    {
        return view('admin.import.medicines');
    }

    /**
     * Process user import with column mapping
     */
    public function processUsers(Request $request)
    {
        $request->validate([
            'file' => 'required|file|mimes:csv,txt,xlsx,xls',
            'f_name_column' => 'required|integer',
            'l_name_column' => 'required|integer',
            'email_column' => 'required|integer',
            'password_column' => 'nullable|integer',
            'user_type_column' => 'nullable|integer',
            'dob_column' => 'nullable|integer',
            'address_column' => 'nullable|integer',
            'gender_column' => 'nullable|integer',
            'contact_no_column' => 'nullable|integer',
            'patient_type_column' => 'nullable|integer',
        ]);

        $file = $request->file('file');
        $data = Excel::toArray([], $file);

        if (empty($data) || empty($data[0])) {
            return back()->with('error', 'The uploaded file is empty or invalid.');
        }

        $rows = $data[0];
        $headers = array_map('trim', $rows[0]);
        $dataRows = array_slice($rows, 1);

        $fNameCol = $request->f_name_column;
        $mNameCol = $request->m_name_column ?? null;
        $lNameCol = $request->l_name_column;
        $emailCol = $request->email_column;
        $passwordCol = $request->password_column ?? null;
        $userTypeCol = $request->user_type_column ?? null;
        $dobCol = $request->filled('dob_column') ? (int) $request->dob_column : null;
        $addressCol = $request->filled('address_column') ? (int) $request->address_column : null;
        $genderCol = $request->filled('gender_column') ? (int) $request->gender_column : null;
        $contactNoCol = $request->filled('contact_no_column') ? (int) $request->contact_no_column : null;
        $patientTypeCol = $request->filled('patient_type_column') ? (int) $request->patient_type_column : null;

        $imported = 0;
        $errors = [];

        DB::beginTransaction();
        try {
            foreach ($dataRows as $index => $row) {
                if (empty(array_filter($row))) continue;

                $fName = isset($row[$fNameCol]) ? trim($row[$fNameCol]) : null;
                $mName = $mNameCol !== null && isset($row[$mNameCol]) ? trim($row[$mNameCol]) : '';
                $lName = isset($row[$lNameCol]) ? trim($row[$lNameCol]) : null;
                $email = isset($row[$emailCol]) ? trim($row[$emailCol]) : null;
                $password = $passwordCol !== null && isset($row[$passwordCol]) && !empty($row[$passwordCol]) ? trim($row[$passwordCol]) : 'password123';
                $userType = $userTypeCol !== null && isset($row[$userTypeCol]) && $row[$userTypeCol] !== '' ? (int) $row[$userTypeCol] : User::TYPE_STUDENT;
                $dobRaw = $dobCol !== null && isset($row[$dobCol]) && !empty($row[$dobCol]) ? trim($row[$dobCol]) : now()->format('Y-m-d');

                if (! in_array($userType, [
                    User::TYPE_ADMIN, User::TYPE_STUDENT, User::TYPE_STAFF, User::TYPE_DOCTOR,
                    User::TYPE_FACULTY, User::TYPE_GUARDIAN, User::TYPE_PRINCIPAL, User::TYPE_TEACHER,
                ], true)) {
                    $errors[] = "Row " . ($index + 2) . ": Invalid user type '{$userType}'.";
                    continue;
                }

                // Try to parse various date formats from Excel
                $dob = $this->parseDate($dobRaw);
                if (!$dob) {
                    $errors[] = "Row " . ($index + 2) . ": Invalid date format for DOB '$dobRaw'. Use YYYY-MM-DD.";
                    continue;
                }
                $address = $addressCol !== null && isset($row[$addressCol]) ? trim($row[$addressCol]) : '';
                $gender = $genderCol !== null && isset($row[$genderCol]) ? trim($row[$genderCol]) : '';
                $contactNo = $contactNoCol !== null && isset($row[$contactNoCol]) ? trim($row[$contactNoCol]) : '';

                if (empty($fName) || empty($lName) || empty($email)) {
                    $errors[] = "Row " . ($index + 2) . ": First Name, Last Name, and Email are required.";
                    continue;
                }

                // Check if email already exists
                if (User::where('email', $email)->exists()) {
                    $errors[] = "Row " . ($index + 2) . ": Email '$email' already exists. Skipped.";
                    continue;
                }

                $fullName = trim($fName . ' ' . $mName . ' ' . $lName);

                $user = User::create([
                    'name' => $fullName,
                    'email' => $email,
                    'password' => Hash::make($password),
                    'user_type' => $userType,
                    'f_name' => $fName,
                    'm_name' => $mName,
                    'l_name' => $lName,
                    'dob' => $dob,
                    'address' => $address,
                    'gender' => $gender,
                    'contact_no' => $contactNo,
                ]);

                // Only student and faculty accounts receive patient profiles automatically.
                if (in_array($userType, [User::TYPE_STUDENT, User::TYPE_FACULTY], true)) {
                    $patientType = $userType === User::TYPE_STUDENT ? 1 : 2;

                    Patients::create([
                        'user_id' => $user->id,
                        'patient_type' => $patientType,
                        'school_id' => '',
                        'edulvl_id' => 0,
                        'address' => $address,
                        'medical_condition' => '',
                        'medical_illness' => '',
                        'operations' => '',
                        'allergies' => '',
                        'emergency_contact_name' => '',
                        'emergency_contact_number' => '',
                        'emergency_relationship' => '',
                    ]);
                }

                $imported++;
            }

            DB::commit();
        } catch (\Exception $e) {
            DB::rollBack();
            return back()->with('error', 'Import failed: ' . $e->getMessage());
        }

        $message = "Successfully imported $imported users.";
        if (!empty($errors)) {
            $message .= " " . count($errors) . " rows skipped due to errors: " . implode('; ', $errors);
        }

        $redirect = auth()->user()->user_type == 0 ? 'admin.medicines.index' : 'staff.dashboard';
        return redirect()->route($redirect)->with('success', $message);
    }

    /**
     * Process medicine import with column mapping
     */
    public function processMedicines(Request $request)
    {
        $request->validate([
            'file' => 'required|file|mimes:csv,txt,xlsx,xls',
            'name_column' => 'required|integer',
            'generic_name_column' => 'nullable|integer',
            'manufacturer_column' => 'nullable|integer',
            'dosage_column' => 'nullable|integer',
            'description_column' => 'nullable|integer',
            'quantity_column' => 'nullable|integer',
            'unit_column' => 'nullable|integer',
            'expiration_date_column' => 'nullable|integer',
            'medicine_type_column' => 'nullable|integer',
            'status_column' => 'nullable|integer',
        ]);

        $file = $request->file('file');
        $data = Excel::toArray([], $file);

        if (empty($data) || empty($data[0])) {
            return back()->with('error', 'The uploaded file is empty or invalid.');
        }

        $rows = $data[0];
        $dataRows = array_slice($rows, 1);

        $nameCol = $request->name_column;
        $genericNameCol = $request->filled('generic_name_column') ? (int) $request->generic_name_column : null;
        $manufacturerCol = $request->filled('manufacturer_column') ? (int) $request->manufacturer_column : null;
        $dosageCol = $request->filled('dosage_column') ? (int) $request->dosage_column : null;
        $descCol = $request->filled('description_column') ? (int) $request->description_column : null;
        $qtyCol = $request->filled('quantity_column') ? (int) $request->quantity_column : null;
        $unitCol = $request->filled('unit_column') ? (int) $request->unit_column : null;
        $expCol = $request->filled('expiration_date_column') ? (int) $request->expiration_date_column : null;
        $typeCol = $request->filled('medicine_type_column') ? (int) $request->medicine_type_column : null;
        $statusCol = $request->filled('status_column') ? (int) $request->status_column : null;

        $imported = 0;
        $errors = [];

        DB::beginTransaction();
        try {
            foreach ($dataRows as $index => $row) {
                if (empty(array_filter($row))) continue;

                $name = isset($row[$nameCol]) ? trim($row[$nameCol]) : null;

                if (empty($name)) {
                    $errors[] = "Row " . ($index + 2) . ": Medicine name is required.";
                    continue;
                }

                $genericName = $genericNameCol !== null && isset($row[$genericNameCol]) ? trim($row[$genericNameCol]) : '';
                $manufacturer = $manufacturerCol !== null && isset($row[$manufacturerCol]) ? trim($row[$manufacturerCol]) : '';
                $dosage = $dosageCol !== null && isset($row[$dosageCol]) ? trim($row[$dosageCol]) : '';
                $description = $descCol !== null && isset($row[$descCol]) ? trim($row[$descCol]) : '';
                $quantity = $qtyCol !== null && isset($row[$qtyCol]) && is_numeric($row[$qtyCol]) ? (int)$row[$qtyCol] : 0;
                $unit = $unitCol !== null && isset($row[$unitCol]) ? trim($row[$unitCol]) : '';
                $expRaw = $expCol !== null && isset($row[$expCol]) && !empty($row[$expCol]) ? trim($row[$expCol]) : null;
                $expirationDate = $expRaw ? $this->parseDate($expRaw) : null;
                $medicineType = $typeCol !== null && isset($row[$typeCol]) ? trim($row[$typeCol]) : 'General';
                $status = $statusCol !== null && isset($row[$statusCol]) ? trim($row[$statusCol]) : 'active';

                // Check if medicine with same name already exists
                if (Medicine::where('name', $name)->exists()) {
                    $medicine = Medicine::where('name', $name)->first();
                    if ($quantity > 0) {
                        $medicine->quantity += $quantity;
                        $medicine->save();
                    }
                    $imported++;
                    continue;
                }

                $medicineData = [
                    'name' => $name,
                    'generic_name' => $genericName,
                    'manufacturer' => $manufacturer,
                    'dosage' => $dosage,
                    'description' => $description,
                    'quantity' => $quantity,
                    'unit' => $unit,
                    'expiration_date' => $expirationDate,
                    'medicine_type' => $medicineType,
                    'status' => $status,
                ];

                Medicine::create($medicineData);
                $imported++;
            }

            DB::commit();
        } catch (\Exception $e) {
            DB::rollBack();
            return back()->with('error', 'Import failed: ' . $e->getMessage());
        }

        $message = "Successfully imported $imported medicines.";
        if (!empty($errors)) {
            $message .= " " . count($errors) . " rows skipped due to errors.";
        }

        $redirect = auth()->user()->user_type == 0 ? 'admin.medicines.index' : 'staff.dashboard';
        return redirect()->route($redirect)->with('success', $message);
    }

    /**
     * Generate sample CSV for users
     */
    public function downloadUsersSample()
    {
        $headers = ['f_name', 'm_name', 'l_name', 'email', 'password', 'user_type', 'dob', 'address', 'gender', 'contact_no'];
        $sampleData = [
            ['Juan', 'Reyes', 'Dela Cruz', 'juan.delacruz@example.com', 'password123', '2', '2000-01-15', '123 Main St, Manila', 'Male', '09171234567'],
            ['Maria', 'Santos', 'Garcia', 'maria.santos@example.com', 'password123', '2', '1998-05-20', '456 Elm St, Quezon City', 'Female', '09187654321'],
        ];

        $content = implode(',', $headers) . "\n";
        foreach ($sampleData as $row) {
            $content .= implode(',', $row) . "\n";
        }

        return response($content, 200, [
            'Content-Type' => 'text/csv',
            'Content-Disposition' => 'attachment; filename="users_sample.csv"',
        ]);
    }

    /**
     * Generate sample CSV for medicines
     */
    public function downloadMedicinesSample()
    {
        $headers = ['name', 'generic_name', 'manufacturer', 'dosage', 'description', 'quantity', 'unit', 'expiration_date', 'medicine_type', 'status'];
        $sampleData = [
            ['Paracetamol 500mg', 'Paracetamol', 'ABC Pharma', '500mg', 'For headache and mild pain', '100', 'tablet', '2026-12-31', 'General', 'active'],
            ['Amoxicillin 250mg', 'Amoxicillin', 'XYZ Meds', '250mg', 'Antibiotic for infections', '50', 'capsule', '2026-06-30', 'Antibiotic', 'active'],
        ];

        $content = implode(',', $headers) . "\n";
        foreach ($sampleData as $row) {
            $content .= implode(',', $row) . "\n";
        }

        return response($content, 200, [
            'Content-Type' => 'text/csv',
            'Content-Disposition' => 'attachment; filename="medicines_sample.csv"',
        ]);
    }
}
