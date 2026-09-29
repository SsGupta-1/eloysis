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
        Schema::table('questions', function (Blueprint $table) {
            $table->foreignId('class_id')
                ->nullable()
                ->after('id')
                ->constrained('academic_classes')
                ->cascadeOnUpdate()
                ->nullOnDelete();

            $table->foreignId('academic_session_id')
                ->nullable()
                ->after('class_id')
                ->constrained('academic_sessions')
                ->cascadeOnUpdate()
                ->nullOnDelete();

            $table->string('chapter_name', 200)->nullable()->after('subject_id');
            $table->string('topic_name', 200)->nullable()->after('chapter_name');

            $table->string('question_type', 50)->default('mcq')->change();

            $table->string('difficulty_level', 20)->default('medium')->after('question_type'); // easy, medium, hard
            $table->string('blooms_taxonomy', 30)->nullable()->after('difficulty_level'); // remember, understand, apply, analyze, evaluate, create

            $table->text('option_a')->nullable()->change();
            $table->text('option_b')->nullable()->change();
            $table->string('correct_option', 50)->nullable()->change();

            $table->json('options_data')->nullable()->after('option_d');
            $table->longText('correct_answer_data')->nullable()->after('options_data');
            $table->json('tags')->nullable()->after('explanation');
            $table->string('attachment_url')->nullable()->after('image_path');

            $table->index('class_id');
            $table->index('academic_session_id');
            $table->index('difficulty_level');
            $table->index('blooms_taxonomy');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('questions', function (Blueprint $table) {
            $table->dropForeign(['class_id']);
            $table->dropForeign(['academic_session_id']);
            $table->dropColumn([
                'class_id',
                'academic_session_id',
                'chapter_name',
                'topic_name',
                'difficulty_level',
                'blooms_taxonomy',
                'options_data',
                'correct_answer_data',
                'tags',
                'attachment_url',
            ]);
        });
    }
};
