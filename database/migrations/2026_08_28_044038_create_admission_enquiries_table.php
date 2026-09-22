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
        Schema::create('admission_enquiries', function (Blueprint $table) {
            $table->id();
            $table->foreignId('academic_session_id')->nullable()->constrained('academic_sessions')->nullOnDelete();
            $table->string('application_no')->nullable()->unique();
            $table->string('student_name');
            $table->string('student_email');
            $table->string('student_phone');
            $table->date('date_of_birth')->nullable();
            $table->string('gender')->nullable();
            $table->string('parent_name');
            $table->string('parent_phone');
            $table->string('alternate_phone')->nullable();
            $table->foreignId('class_id')->nullable()->constrained('academic_classes')->nullOnDelete();
            $table->enum('source', ['website', 'walk_in', 'reference', 'google', 'facebook', 'instagram', 'advertisement', 'other'])->default('website');

            /*
            |--------------------------------------------------------------------------
            | Reference Information
            |--------------------------------------------------------------------------
            |
            | reference_type + reference_id can point to different
            | entities such as student, parent, teacher or staff.
            |
            */

            $table->string('reference_type')->nullable();
            $table->unsignedBigInteger('reference_id')->nullable();
            $table->string('reference_name')->nullable();
            $table->string('reference_phone')->nullable();

            /*
            |--------------------------------------------------------------------------
            | Enquiry Details
            |--------------------------------------------------------------------------
            */

            $table->text('message')->nullable();
            $table->text('remarks')->nullable();

            /*
            |--------------------------------------------------------------------------
            | Enquiry Status
            |--------------------------------------------------------------------------
            |
            | new
            | contacted
            | follow_up
            | interested
            | not_interested
            | admitted
            | cancelled
            |
            */

            $table->enum('status', ['new', 'contacted', 'follow_up', 'interested', 'not_interested', 'admitted', 'cancelled'])->default('new');

            /*
            |--------------------------------------------------------------------------
            | Follow Up
            |--------------------------------------------------------------------------
            */

            $table->date('follow_up_date')->nullable();
            $table->timestamp('last_contacted_at')->nullable();

            $table->timestamp('next_follow_up_at')->nullable();

            /*
            |--------------------------------------------------------------------------
            | Staff / Admin Handling Enquiry
            |--------------------------------------------------------------------------
            */

            $table->foreignId('handled_by')->nullable()->constrained('users')->nullOnDelete();

            /*
            |--------------------------------------------------------------------------
            | Website Tracking
            |--------------------------------------------------------------------------
            */

            $table->ipAddress('ip_address')->nullable();
            $table->text('user_agent')->nullable();
            $table->index(['academic_session_id', 'status']);
            $table->index('source');
            $table->index(['reference_type', 'reference_id']);

            $table->index('class_id');

            $table->index('parent_phone');

            $table->index('follow_up_date');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('admission_enquiries');
    }
};
