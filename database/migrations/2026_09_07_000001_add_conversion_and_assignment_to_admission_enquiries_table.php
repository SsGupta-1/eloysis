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
        Schema::table('admission_enquiries', function (Blueprint $table) {
            $table->string('status', 50)->default('new')->change();
            $table->foreignId('assigned_to')->nullable()->after('handled_by')->constrained('users')->nullOnDelete();
            $table->timestamp('assigned_at')->nullable()->after('assigned_to');
            $table->foreignId('assigned_by')->nullable()->after('assigned_at')->constrained('users')->nullOnDelete();
            $table->unsignedInteger('attempt_count')->default(0)->after('assigned_by');
            $table->timestamp('last_attempt_at')->nullable()->after('attempt_count');
            $table->string('last_attempt_status', 50)->nullable()->after('last_attempt_at');
            $table->timestamp('converted_at')->nullable()->after('last_attempt_status');
            $table->foreignId('converted_by')->nullable()->after('converted_at')->constrained('users')->nullOnDelete();
            $table->foreignId('student_profile_id')->nullable()->after('converted_by')->constrained('student_profiles')->nullOnDelete();
            $table->foreignId('enrollment_id')->nullable()->after('student_profile_id')->constrained('student_enrollments')->nullOnDelete();
            $table->foreignId('converted_user_id')->nullable()->after('enrollment_id')->constrained('users')->nullOnDelete();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('admission_enquiries', function (Blueprint $table) {
            $table->dropForeign(['assigned_to']);
            $table->dropForeign(['assigned_by']);
            $table->dropForeign(['converted_by']);
            $table->dropForeign(['student_profile_id']);
            $table->dropForeign(['enrollment_id']);
            $table->dropForeign(['converted_user_id']);
            $table->dropColumn([
                'assigned_to',
                'assigned_at',
                'assigned_by',
                'attempt_count',
                'last_attempt_at',
                'last_attempt_status',
                'converted_at',
                'converted_by',
                'student_profile_id',
                'enrollment_id',
                'converted_user_id',
            ]);
        });
    }
};
