<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;

class TeacherProfile extends Model
{
    use SoftDeletes;

    protected $fillable = [

        'user_id',

        'employee_id',

        'qualification',

        'specialization',

        'joining_date',

        'dob',

        'gender',

        'experience_years',

        'address',

        'city',

        'state',

        'pincode',

        'emergency_contact_name',

        'emergency_contact_mobile',

    ];

    protected $casts = [

        'joining_date' => 'date',

        'dob' => 'date',

    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function attendances(): HasMany
    {
        return $this->hasMany(
            TeacherAttendance::class,
            'teacher_profile_id'
        );
    }
}
