<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Course extends Model
{
    use HasFactory;
    protected $table = 'courses';

    protected $fillable = [
        'course_name',
        'course_description',
    ];

    /**
     * Course to Patients Relationship (One-to-Many)
     */
    public function patients(): HasMany
    {
        return $this->hasMany(Patients::class, 'course_id', 'id');
    }
}
