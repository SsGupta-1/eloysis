<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class AcademicClass extends Model
{
    protected $table = 'academic_classes';

    protected $fillable = [
        'class_name',
        'class_code',
        'description',
        'status',
    ];

    protected $casts = [
        'status' => 'boolean',
    ];

    public function scopeActive($query)
    {
        return $query->where('status', 1);
    }

    /**
     * One class has many student enrollments
     */
    public function enrollments(): HasMany
    {
        return $this->hasMany(StudentEnrollment::class, 'class_id');
    }

    /**
     * One class has many student profiles
     */
    public function studentProfiles(): HasMany
    {
        return $this->hasMany(StudentProfile::class, 'class_id');
    }

    /**
     * One class has many subjects
     */
    public function subjects(): HasMany
    {
        return $this->hasMany(Subject::class, 'class_id');
    }

    /**
     * One class has many exams
     */
    public function exams(): HasMany
    {
        return $this->hasMany(Exam::class, 'class_id');
    }

    public function teacherSubjects()
    {
        return $this->hasMany(
            TeacherSubject::class,
            'class_id'
        );
    }
}
