<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class TeacherAttendance extends Model
{

    protected $fillable = [
        'teacher_profile_id',
        'attendance_date',
        'status',
        'remarks',
    ];

    protected $casts = [
        'attendance_date' => 'date',
    ];

    /**
     * Teacher
     */
    public function teacher_profile(): BelongsTo
    {
        return $this->belongsTo(
            TeacherProfile::class,
            'teacher_profile_id'
        );
    }
}
