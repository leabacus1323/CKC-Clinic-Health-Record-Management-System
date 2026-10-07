<?php

namespace App\Exports;

use App\Models\PrescriptionRecord;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;

class PrescriptionExport implements FromCollection, WithHeadings
{
    public function collection()
    {
        return PrescriptionRecord::join('users', 'users.id', '=', 'prescription_records.patient_id')
            ->leftJoin('medicines', 'medicines.id', '=', 'prescription_records.medicine_id')
            ->select(
                'prescription_records.id',
                'users.name as patient_name',
                'users.email',
                'medicines.name as medicine_name',
                'prescription_records.dosage',
                'prescription_records.frequency',
                'prescription_records.duration',
                'prescription_records.instruction',
                'prescription_records.status',
                'prescription_records.date_prescribed',
                'prescription_records.date_discontinued',
                'prescription_records.discontinuation_reason',
                'prescription_records.created_at'
            )
            ->get();
    }

    public function headings(): array
    {
        return [
            'ID',
            'Patient Name',
            'Patient Email',
            'Medicine Name',
            'Dosage',
            'Frequency',
            'Duration',
            'Instructions',
            'Status',
            'Date Prescribed',
            'Date Discontinued',
            'Discontinuation Reason',
            'Created At'
        ];
    }
}
