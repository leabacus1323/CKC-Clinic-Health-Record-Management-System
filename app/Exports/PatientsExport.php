<?php

namespace App\Exports;

use App\Models\Patients;
use App\Models\User;
use Illuminate\Support\Facades\DB;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;

class PatientsExport implements FromCollection, WithHeadings
{
    public function collection()
    {
        return Patients::join('users', 'users.id', '=', 'patients.user_id')
            ->leftJoin('educational_level', 'educational_level.id', '=', 'patients.edulvl_id')
            ->whereIn('users.user_type', [1, 2])
            ->select(
                'patients.id',
                'users.name as patient_name',
                'users.email',
                DB::raw("CASE WHEN patients.patient_type = 1 THEN 'Student' WHEN patients.patient_type = 3 THEN 'Faculty' ELSE 'Staff' END as patient_type"),
                'patients.school_id',
                'patients.bloodtype',
                'patients.address',
                'patients.medical_condition',
                'patients.allergies',
                'patients.emergency_contact_name',
                'patients.emergency_contact_number',
                'patients.emergency_relationship',
                'educational_level.level_name as educational_level',
                'patients.created_at'
            )
            ->get();
    }

    public function headings(): array
    {
        return [
            'ID',
            'Patient Name',
            'Email',
            'Patient Type',
            'School ID',
            'Blood Type',
            'Address',
            'Medical Condition',
            'Allergies',
            'Emergency Contact Name',
            'Emergency Contact Number',
            'Emergency Contact Relationship',
            'Educational Level',
            'Created At'
        ];
    }
}
