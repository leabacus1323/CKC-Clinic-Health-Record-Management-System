<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Medicine extends Model
{
    use HasFactory;
    protected $table = 'medicine';

    protected $fillable = [
        'name',
        'generic_name',
        'manufacturer',
        'description',
        'quantity',
        'dosage',
        'unit',
        'expiration_date',
        'medicine_type'
    ];
}
