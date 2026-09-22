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
        Schema::create('admission_enquiry_followups', function (Blueprint $table) {
            $table->id();
            $table->foreignId('admission_enquiry_id')->constrained('admission_enquiries')->cascadeOnDelete();
            $table->foreignId('user_id')->nullable()->constrained('users')->nullOnDelete();
            $table->string('action_type', 50)->default('follow_up'); // follow_up, status_change, assignment, remark, converted
            $table->string('status', 50)->nullable();
            $table->string('attempt_status', 50)->nullable();
            $table->text('remarks')->nullable();
            $table->dateTime('next_follow_up_at')->nullable();
            $table->timestamps();

            $table->index(['admission_enquiry_id', 'created_at'], 'aef_enquiry_created_idx');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('admission_enquiry_followups');
    }
};
