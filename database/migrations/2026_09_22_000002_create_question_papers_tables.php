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
        Schema::create('question_papers', function (Blueprint $table) {
            $table->id();
            $table->foreignId('academic_session_id')
                ->nullable()
                ->constrained('academic_sessions')
                ->cascadeOnUpdate()
                ->nullOnDelete();

            $table->foreignId('class_id')
                ->constrained('academic_classes')
                ->cascadeOnUpdate()
                ->restrictOnDelete();

            $table->foreignId('subject_id')
                ->constrained('subjects')
                ->cascadeOnUpdate()
                ->restrictOnDelete();

            $table->foreignId('exam_id')
                ->nullable()
                ->constrained('exams')
                ->cascadeOnUpdate()
                ->nullOnDelete();

            $table->string('title', 255);
            $table->string('paper_code', 100)->unique();
            $table->decimal('total_marks', 8, 2)->default(100.00);
            $table->integer('duration_minutes')->default(180);
            $table->longText('instructions')->nullable();

            $table->boolean('is_confidential')->default(true);
            $table->boolean('is_locked')->default(false);
            $table->enum('approval_status', ['draft', 'pending_approval', 'approved', 'rejected'])->default('draft');
            $table->foreignId('approved_by')
                ->nullable()
                ->constrained('users')
                ->nullOnDelete();
            $table->timestamp('approved_at')->nullable();

            $table->string('version', 20)->default('1.0');
            $table->boolean('has_sets')->default(false);
            $table->json('set_names')->nullable(); // ['A', 'B', 'C', 'D']
            $table->boolean('shuffle_questions')->default(false);
            $table->boolean('shuffle_options')->default(false);

            $table->enum('status', ['draft', 'published', 'archived'])->default('draft');

            $table->foreignId('created_by')
                ->nullable()
                ->constrained('users')
                ->cascadeOnUpdate()
                ->nullOnDelete();

            $table->timestamps();
            $table->softDeletes();

            $table->index('academic_session_id');
            $table->index('class_id');
            $table->index('subject_id');
            $table->index('approval_status');
            $table->index('status');
        });

        Schema::create('question_paper_sections', function (Blueprint $table) {
            $table->id();
            $table->foreignId('question_paper_id')
                ->constrained('question_papers')
                ->cascadeOnDelete();

            $table->string('section_name', 150); // e.g. "Section A - Multiple Choice"
            $table->string('section_type', 50)->default('mcq');
            $table->integer('total_questions')->default(0);
            $table->decimal('marks_per_question', 6, 2)->nullable();
            $table->text('instructions')->nullable();
            $table->integer('sort_order')->default(1);
            $table->timestamps();

            $table->index('question_paper_id');
            $table->index('sort_order');
        });

        Schema::create('question_paper_items', function (Blueprint $table) {
            $table->id();
            $table->foreignId('question_paper_id')
                ->constrained('question_papers')
                ->cascadeOnDelete();

            $table->foreignId('section_id')
                ->nullable()
                ->constrained('question_paper_sections')
                ->cascadeOnDelete();

            $table->foreignId('question_id')
                ->constrained('questions')
                ->cascadeOnDelete();

            $table->string('set_code', 20)->default('ALL'); // 'ALL', 'A', 'B', 'C', 'D'
            $table->integer('display_order')->default(1);
            $table->decimal('marks', 6, 2)->default(1.00);
            $table->timestamps();

            $table->index(['question_paper_id', 'set_code']);
            $table->index('section_id');
            $table->index('question_id');
        });

        Schema::create('question_paper_audits', function (Blueprint $table) {
            $table->id();
            $table->foreignId('question_paper_id')
                ->constrained('question_papers')
                ->cascadeOnDelete();

            $table->foreignId('user_id')
                ->nullable()
                ->constrained('users')
                ->nullOnDelete();

            $table->string('action', 100); // created, updated, viewed, locked, unlocked, approved, rejected, downloaded_pdf, printed
            $table->text('details')->nullable();
            $table->string('ip_address', 50)->nullable();
            $table->timestamp('created_at')->useCurrent();

            $table->index('question_paper_id');
            $table->index('user_id');
            $table->index('action');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('question_paper_audits');
        Schema::dropIfExists('question_paper_items');
        Schema::dropIfExists('question_paper_sections');
        Schema::dropIfExists('question_papers');
    }
};
