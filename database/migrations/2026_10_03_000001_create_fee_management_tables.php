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
        // 1. Fee Heads (e.g. Tuition Fee, Admission Fee, Exam Fee, Transport, Library)
        Schema::create('fee_heads', function (Blueprint $table) {
            $table->id();
            $table->string('name', 100);
            $table->string('code', 50)->unique()->nullable();
            $table->text('description')->nullable();
            $table->boolean('is_active')->default(true);
            $table->timestamps();
            $table->softDeletes();
        });

        // 2. Fee Groups (e.g. General Primary Fee, Science Stream Slabs)
        Schema::create('fee_groups', function (Blueprint $table) {
            $table->id();
            $table->string('name', 100);
            $table->text('description')->nullable();
            $table->boolean('is_active')->default(true);
            $table->timestamps();
            $table->softDeletes();
        });

        // 3. Fee Discounts / Concessions / Scholarships
        Schema::create('fee_discounts', function (Blueprint $table) {
            $table->id();
            $table->string('name', 100);
            $table->string('code', 50)->unique()->nullable();
            $table->enum('discount_type', ['percentage', 'fixed'])->default('fixed');
            $table->decimal('amount', 10, 2)->default(0);
            $table->text('description')->nullable();
            $table->boolean('is_active')->default(true);
            $table->timestamps();
            $table->softDeletes();
        });

        // 4. Fee Structures (Class & Session wise pricing & due dates)
        Schema::create('fee_structures', function (Blueprint $table) {
            $table->id();
            $table->foreignId('academic_session_id')
                ->constrained('academic_sessions')
                ->cascadeOnDelete();

            $table->foreignId('academic_class_id')
                ->nullable()
                ->constrained('academic_classes')
                ->cascadeOnDelete();

            $table->foreignId('fee_head_id')
                ->constrained('fee_heads')
                ->cascadeOnDelete();

            $table->foreignId('fee_group_id')
                ->nullable()
                ->constrained('fee_groups')
                ->nullOnDelete();

            $table->decimal('amount', 10, 2);
            $table->enum('frequency', ['one_time', 'monthly', 'quarterly', 'half_yearly', 'annually'])->default('monthly');
            $table->date('due_date')->nullable();
            $table->enum('fine_type', ['none', 'flat', 'percentage', 'daily'])->default('none');
            $table->decimal('fine_amount', 10, 2)->default(0);
            $table->boolean('is_active')->default(true);
            $table->timestamps();
            $table->softDeletes();

            $table->index(['academic_session_id', 'academic_class_id']);
        });

        // 5. Student Fee Allocations (Assigned Fee items / bills for student)
        Schema::create('student_fee_allocations', function (Blueprint $table) {
            $table->id();
            $table->foreignId('student_enrollment_id')
                ->constrained('student_enrollments')
                ->cascadeOnDelete();

            $table->foreignId('stu_profile_id')
                ->constrained('student_profiles')
                ->cascadeOnDelete();

            $table->foreignId('academic_session_id')
                ->constrained('academic_sessions')
                ->cascadeOnDelete();

            $table->foreignId('fee_structure_id')
                ->nullable()
                ->constrained('fee_structures')
                ->nullOnDelete();

            $table->foreignId('fee_head_id')
                ->constrained('fee_heads')
                ->cascadeOnDelete();

            $table->foreignId('fee_discount_id')
                ->nullable()
                ->constrained('fee_discounts')
                ->nullOnDelete();

            $table->string('title', 150); // e.g., "April 2026 - Tuition Fee"
            $table->unsignedTinyInteger('month')->nullable(); // 1 to 12
            $table->unsignedSmallInteger('year')->nullable(); // 2026
            $table->date('due_date');

            $table->decimal('amount', 10, 2); // Base amount
            $table->decimal('discount_amount', 10, 2)->default(0);
            $table->decimal('fine_amount', 10, 2)->default(0);
            $table->decimal('paid_amount', 10, 2)->default(0);

            $table->enum('status', ['unpaid', 'partial', 'paid', 'waived'])->default('unpaid');
            $table->timestamps();

            $table->index(['student_enrollment_id', 'status']);
            $table->index(['stu_profile_id', 'due_date']);
        });

        // 6. Fee Payments (Receipts / Transactions)
        Schema::create('fee_payments', function (Blueprint $table) {
            $table->id();
            $table->string('receipt_no', 60)->unique();

            $table->foreignId('student_enrollment_id')
                ->constrained('student_enrollments')
                ->cascadeOnDelete();

            $table->foreignId('stu_profile_id')
                ->constrained('student_profiles')
                ->cascadeOnDelete();

            $table->foreignId('academic_session_id')
                ->constrained('academic_sessions')
                ->cascadeOnDelete();

            $table->date('payment_date');
            $table->decimal('subtotal_amount', 10, 2);
            $table->decimal('discount_amount', 10, 2)->default(0);
            $table->decimal('fine_amount', 10, 2)->default(0);
            $table->decimal('total_paid', 10, 2);

            $table->enum('payment_mode', ['cash', 'cheque', 'dd', 'upi', 'card', 'bank_transfer', 'online'])->default('cash');
            $table->string('transaction_reference', 100)->nullable();
            $table->string('bank_name', 100)->nullable();
            $table->date('cheque_date')->nullable();

            $table->foreignId('collected_by')
                ->nullable()
                ->constrained('users')
                ->nullOnDelete();

            $table->enum('status', ['successful', 'cancelled', 'bounced'])->default('successful');
            $table->text('remarks')->nullable();
            $table->timestamps();
            $table->softDeletes();

            $table->index(['payment_date', 'status']);
            $table->index('receipt_no');
        });

        // 7. Fee Payment Items (Details of which allocations were paid in this receipt)
        Schema::create('fee_payment_items', function (Blueprint $table) {
            $table->id();
            $table->foreignId('fee_payment_id')
                ->constrained('fee_payments')
                ->cascadeOnDelete();

            $table->foreignId('student_fee_allocation_id')
                ->constrained('student_fee_allocations')
                ->cascadeOnDelete();

            $table->decimal('amount_paid', 10, 2);
            $table->decimal('discount_applied', 10, 2)->default(0);
            $table->decimal('fine_paid', 10, 2)->default(0);
            $table->timestamps();

            $table->index('fee_payment_id');
            $table->index('student_fee_allocation_id');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('fee_payment_items');
        Schema::dropIfExists('fee_payments');
        Schema::dropIfExists('student_fee_allocations');
        Schema::dropIfExists('fee_structures');
        Schema::dropIfExists('fee_discounts');
        Schema::dropIfExists('fee_groups');
        Schema::dropIfExists('fee_heads');
    }
};
