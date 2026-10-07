<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Foundation\Auth\User as Authenticatable;

class Admin extends Authenticatable
{
    use HasFactory;

    protected $fillable = [
        'name',
        'email',
        'password',
        'email',
        'user_type',
        'password',
        'f_name',
        'm_name',
        'l_name',
        'dob',
        'address',
        'gender',
        'contact_no',
    ];
}
