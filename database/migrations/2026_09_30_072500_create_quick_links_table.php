<?php

use App\Models\QuickLink;
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
        if (! Schema::hasTable('quick_links')) {
            Schema::create('quick_links', function (Blueprint $table) {
                $table->id();
                $table->string('title');
                $table->string('description')->nullable();
                $table->string('icon')->nullable()->default('bi-mortarboard-fill');
                $table->string('url')->nullable();
                $table->string('color')->default('primary');
                $table->integer('sort_order')->default(0);
                $table->boolean('status')->default(true);
                $table->timestamps();
            });

            // Seed default quick links
            $defaults = [
                [
                    'title' => 'Admission Open',
                    'description' => 'Apply for new admission.',
                    'icon' => 'bi-mortarboard-fill',
                    'url' => '/admission-enquiry',
                    'color' => 'primary',
                    'sort_order' => 1,
                    'status' => true,
                ],
                [
                    'title' => 'Online Exam',
                    'description' => 'Start your online examination.',
                    'icon' => 'bi-laptop',
                    'url' => '/admin/login',
                    'color' => 'success',
                    'sort_order' => 2,
                    'status' => true,
                ],
                [
                    'title' => 'Latest Results',
                    'description' => 'Check examination results.',
                    'icon' => 'bi-award-fill',
                    'url' => '/news',
                    'color' => 'warning',
                    'sort_order' => 3,
                    'status' => true,
                ],
            ];

            foreach ($defaults as $link) {
                QuickLink::create($link);
            }
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('quick_links');
    }
};
