<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class GeneratedReport extends Model
{
    protected $fillable = [
        'title',
        'report_type',
        'description',
        'parameters',
        'file_path',
        'generated_by_type',
        'generated_by_id',
        'status',
    ];

    protected $casts = [
        'parameters' => 'array',
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
    ];

    public function generatedBy()
    {
        return $this->morphTo(__FUNCTION__, 'generated_by_type', 'generated_by_id');
    }
}
