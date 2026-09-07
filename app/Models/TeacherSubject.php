<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class TeacherSubject extends Model
{
    use HasFactory;

    protected $fillable = [

        'teacher_id',
        'class_id',
        'section_id',
        'subject_id',
        'status',

    ];

    protected $casts = [

        'status' => 'boolean',

    ];

    /*
    |--------------------------------------------------------------------------
    | Relationships
    |--------------------------------------------------------------------------
    */

    public function teacher()
    {
        return $this->belongsTo(User::class);
    }

    public function subjectClass()
    {
        return $this->belongsTo(
            AcademicClass::class,
            'class_id'
        );
    }

    public function subject()
    {
        return $this->belongsTo(Subject::class);
    }

    public function section()
    {
        return $this->belongsTo(Section::class);
    }
}
