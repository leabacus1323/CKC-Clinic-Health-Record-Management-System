<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use App\Models\Patients;
use App\Models\Doctors;

class User extends Authenticatable
{
    use HasFactory, Notifiable;

    public const TYPE_ADMIN = 0;
    public const TYPE_STUDENT = 1;
    public const TYPE_STAFF = 2;
    public const TYPE_DOCTOR = 3;
    public const TYPE_FACULTY = 4;
    public const TYPE_GUARDIAN = 5;
    public const TYPE_PRINCIPAL = 6;
    public const TYPE_TEACHER = 7;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'name',
        'email',
        'password',
        'user_type',
        'f_name',
        'm_name',
        'l_name',
        'dob',
        'address',
        'gender',
        'contact_no',
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var array<int, string>
     */
    protected $hidden = [
        'password',
        'remember_token',
        'privacy_terms_accepted_ip',
        'privacy_terms_accepted_user_agent',
    ];

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'user_type' => 'integer',
            'password' => 'hashed',
            'dob' => 'date',
            'privacy_terms_accepted_at' => 'datetime',
        ];
    }

    /**
     * Determine whether the user accepted the currently published agreement.
     */
    public function hasAcceptedCurrentPrivacyTerms(): bool
    {
        return $this->privacy_terms_accepted_at !== null
            && hash_equals(
                (string) config('privacy.terms_version'),
                (string) $this->privacy_terms_version
            );
    }

    /**
     * User to Patient Relationship (One-to-One)
     */
    public function patient()
    {
        return $this->hasOne(Patients::class,'user_id','id');
    }

    /**
     * User to Doctor Relationship (One-to-One)
     */
    public function doctor()
    {
        return $this->hasOne(Doctors::class,'user_id','id');
    }

    public function guardianLinks()
    {
        return $this->hasMany(PatientGuardian::class, 'guardian_user_id');
    }

    public function grantedAuthorizations()
    {
        return $this->hasMany(DataAuthorization::class, 'grantor_user_id');
    }

    public function receivedAuthorizations()
    {
        return $this->hasMany(DataAuthorization::class, 'recipient_user_id');
    }

    public function isClinicalProvider(): bool
    {
        return in_array($this->user_type, [self::TYPE_STAFF, self::TYPE_DOCTOR], true);
    }

    public function roleLabel(): string
    {
        return match ($this->user_type) {
            self::TYPE_ADMIN => 'System Administrator',
            self::TYPE_STUDENT => 'Student',
            self::TYPE_STAFF => 'School Nurse / Clinic Staff',
            self::TYPE_DOCTOR => 'School Doctor',
            self::TYPE_FACULTY => 'Faculty Patient',
            self::TYPE_GUARDIAN => 'Guardian',
            self::TYPE_PRINCIPAL => 'Emergency Administrator / Principal',
            self::TYPE_TEACHER => 'Teacher',
            default => 'Unknown',
        };
    }
}
