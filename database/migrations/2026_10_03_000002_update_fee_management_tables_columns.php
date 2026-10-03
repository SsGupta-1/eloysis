<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('student_fee_allocations', function (Blueprint $table) {
            $table->string('month', 50)->nullable()->change();
        });

        Schema::table('fee_payments', function (Blueprint $table) {
            $table->string('payment_mode', 50)->default('cash')->change();
            $table->string('status', 50)->default('paid')->change();
        });
    }

    public function down(): void
    {
        Schema::table('student_fee_allocations', function (Blueprint $table) {
            $table->unsignedTinyInteger('month')->nullable()->change();
        });
    }
};
