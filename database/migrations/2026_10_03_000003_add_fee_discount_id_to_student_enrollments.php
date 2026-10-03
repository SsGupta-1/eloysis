<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('student_enrollments', function (Blueprint $table) {
            $table->foreignId('fee_discount_id')
                ->nullable()
                ->after('section_id')
                ->constrained('fee_discounts')
                ->nullOnDelete();
        });
    }

    public function down(): void
    {
        Schema::table('student_enrollments', function (Blueprint $table) {
            $table->dropForeign(['fee_discount_id']);
            $table->dropColumn('fee_discount_id');
        });
    }
};
