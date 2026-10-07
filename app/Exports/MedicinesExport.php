<?php

namespace App\Exports;

use App\Models\Medicine;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;

class MedicinesExport implements FromCollection, WithHeadings
{
    public function collection()
    {
        return Medicine::select('id', 'name', 'description', 'quantity', 'expiration_date', 'medicine_type', 'created_at', 'updated_at')
            ->get();
    }

    public function headings(): array
    {
        return [
            'ID',
            'Name',
            'Description',
            'Quantity',
            'Expiration Date',
            'Medicine Type',
            'Created At',
            'Updated At'
        ];
    }
}
