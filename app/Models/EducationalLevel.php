<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class EducationalLevel extends Model
{
    use HasFactory;

    protected $table = 'educational_level';

    protected $fillable = [
        'level_name',
        'year_level',
    ];

    /**
     * Educational Level to Patients Relationship (One-to-Many)
     */
    public function patients(): HasMany
    {
        return $this->hasMany(Patients::class, 'edulvl_id', 'id');
    }
}
