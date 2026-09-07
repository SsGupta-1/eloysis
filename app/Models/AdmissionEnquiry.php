<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class AdmissionEnquiry extends Model
{
    protected $fillable = [
        'academic_session_id',
        'application_no',
        'student_name',
        'student_email',
        'student_phone',
        'alternate_phone',
        'date_of_birth',
        'gender',
        'parent_name',
        'parent_phone',
        'class_id',
        'source',
        'reference_type',
        'reference_id',
        'reference_name',
        'reference_phone',
        'message',
        'remarks',
        'status',
        'follow_up_date',
        'last_contacted_at',
        'next_follow_up_at',
        'handled_by',
        'ip_address',
        'user_agent',
    ];

    public function academicSession()
    {
        return $this->belongsTo(AcademicSession::class);
    }

    public function class()
    {
        return $this->belongsTo(AcademicClass::class);
    }

    public function handledBy()
    {
        return $this->belongsTo(User::class, 'handled_by');
    }

    public function scopeActive($query)
    {
        return $query->where('status', '!=', 'cancelled');
    }

    public function scopeForAcademicSession($query, $academicSessionId)
    {
        return $query->where('academic_session_id', $academicSessionId);
    }
}
