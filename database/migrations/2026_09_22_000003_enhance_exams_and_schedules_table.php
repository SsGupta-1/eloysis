<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        // 1. Enhance exams table
        Schema::table('exams', function (Blueprint $table) {
            $table->foreignId('academic_session_id')
                ->nullable()
                ->after('id')
                ->constrained('academic_sessions')
                ->cascadeOnUpdate()
                ->nullOnDelete();

            $table->string('exam_type', 50)->default('unit_test')->after('exam_code'); // unit_test, mid_term, quarterly, half_yearly, annual, practical, entrance, mock, other
            $table->string('exam_mode', 30)->default('both')->after('exam_type'); // offline, online, both

            $table->date('start_date')->nullable()->after('end_at');
            $table->date('end_date')->nullable()->after('start_date');

            $table->boolean('is_published')->default(false)->after('status');
            $table->unsignedBigInteger('grading_scale_id')->nullable()->after('is_published');

            $table->index('academic_session_id');
            $table->index('exam_type');
            $table->index('exam_mode');
        });

        // 2. Create exam_schedules table
        Schema::create('exam_schedules', function (Blueprint $table) {
            $table->id();
            $table->foreignId('exam_id')
                ->constrained('exams')
                ->cascadeOnDelete();

            $table->foreignId('class_id')
                ->constrained('academic_classes')
                ->cascadeOnUpdate()
                ->restrictOnDelete();

            $table->foreignId('section_id')
                ->nullable()
                ->constrained('sections')
                ->cascadeOnUpdate()
                ->nullOnDelete();

            $table->foreignId('subject_id')
                ->constrained('subjects')
                ->cascadeOnUpdate()
                ->restrictOnDelete();

            $table->foreignId('question_paper_id')
                ->nullable()
                ->constrained('question_papers')
                ->cascadeOnUpdate()
                ->nullOnDelete();

            $table->date('exam_date');
            $table->time('start_time');
            $table->time('end_time');
            $table->integer('duration_minutes')->default(180);

            $table->string('room_no', 100)->nullable();
            $table->foreignId('invigilator_id')
                ->nullable()
                ->constrained('users')
                ->nullOnDelete();

            $table->decimal('max_theory_marks', 6, 2)->default(80.00);
            $table->decimal('max_practical_marks', 6, 2)->default(0.00);
            $table->decimal('max_internal_marks', 6, 2)->default(20.00);
            $table->decimal('max_viva_marks', 6, 2)->default(0.00);
            $table->decimal('total_marks', 6, 2)->default(100.00);
            $table->decimal('passing_marks', 6, 2)->default(33.00);

            $table->enum('status', ['scheduled', 'in_progress', 'completed', 'cancelled'])->default('scheduled');

            $table->foreignId('created_by')
                ->nullable()
                ->constrained('users')
                ->nullOnDelete();

            $table->timestamps();

            $table->index(['exam_id', 'class_id']);
            $table->index('exam_date');
            $table->index('status');
        });

        // 3. Create exam_student_enrollments table
        Schema::create('exam_student_enrollments', function (Blueprint $table) {
            $table->id();
            $table->foreignId('exam_id')
                ->constrained('exams')
                ->cascadeOnDelete();

            $table->foreignId('student_enrollment_id')
                ->constrained('student_enrollments')
                ->cascadeOnDelete();

            $table->foreignId('student_id')
                ->constrained('student_profiles')
                ->cascadeOnDelete();

            $table->enum('eligibility_status', ['eligible', 'detained', 'exempted', 'fee_defaulter'])->default('eligible');
            $table->boolean('admit_card_generated')->default(false);
            $table->enum('attendance_status', ['present', 'absent', 'medical_leave'])->default('present');
            $table->string('remarks', 255)->nullable();
            $table->timestamps();

            $table->unique(['exam_id', 'student_enrollment_id'], 'exam_stu_enr_unique');
            $table->index(['exam_id', 'eligibility_status']);
        });

        // 4. Create exam_marks table
        Schema::create('exam_marks', function (Blueprint $table) {
            $table->id();
            $table->foreignId('exam_schedule_id')
                ->constrained('exam_schedules')
                ->cascadeOnDelete();

            $table->foreignId('student_enrollment_id')
                ->constrained('student_enrollments')
                ->cascadeOnDelete();

            $table->decimal('theory_marks', 6, 2)->nullable();
            $table->decimal('practical_marks', 6, 2)->nullable();
            $table->decimal('internal_marks', 6, 2)->nullable();
            $table->decimal('viva_marks', 6, 2)->nullable();
            $table->decimal('total_marks', 6, 2)->nullable();

            $table->boolean('is_absent')->default(false);
            $table->boolean('is_exempted')->default(false);

            $table->decimal('grade_point', 4, 2)->nullable();
            $table->string('letter_grade', 10)->nullable();
            $table->string('remarks', 255)->nullable();

            $table->foreignId('entered_by')
                ->nullable()
                ->constrained('users')
                ->nullOnDelete();

            $table->foreignId('verified_by')
                ->nullable()
                ->constrained('users')
                ->nullOnDelete();

            $table->timestamps();

            $table->unique(['exam_schedule_id', 'student_enrollment_id'], 'exam_marks_unique');
            $table->index('student_enrollment_id');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('exam_marks');
        Schema::dropIfExists('exam_student_enrollments');
        Schema::dropIfExists('exam_schedules');

        Schema::table('exams', function (Blueprint $table) {
            $table->dropForeign(['academic_session_id']);
            $table->dropColumn([
                'academic_session_id',
                'exam_type',
                'exam_mode',
                'start_date',
                'end_date',
                'is_published',
                'grading_scale_id',
            ]);
        });
    }
};
