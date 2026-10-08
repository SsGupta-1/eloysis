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
        Schema::create('activity_logs', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->nullable()->constrained('users')->nullOnDelete();
            $table->string('user_name')->nullable();
            $table->string('user_email')->nullable();
            $table->string('guard', 30)->nullable();
            $table->string('module', 50)->nullable()->index();
            $table->string('action', 50)->index();
            $table->string('description', 255)->nullable();
            $table->string('method', 10);
            $table->string('route_name', 150)->nullable()->index();
            $table->text('url');
            $table->string('ip', 45)->nullable();
            $table->text('user_agent')->nullable();
            $table->smallInteger('status_code')->nullable();
            $table->float('duration_ms')->nullable();
            $table->json('payload')->nullable();
            $table->timestamp('created_at')->useCurrent()->index();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('activity_logs');
    }
};
