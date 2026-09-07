<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ClassTimetables extends Model
{
    protected $fillable = [
        'academic_session_id',
        'teacher_subject_id',
        'day',
        'period_id',
        'status',
    ];

    public function academicSession()
    {
        return $this->belongsTo(AcademicSession::class);
    }

    public function period()
    {
        return $this->belongsTo(Periods::class);
    }

    public function teacherSubject()
    {
        return $this->belongsTo(TeacherSubject::class,'teacher_subject_id');
    }

}
    