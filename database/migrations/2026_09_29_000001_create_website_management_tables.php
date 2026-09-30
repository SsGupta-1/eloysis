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
        // 1. Home Sliders
        Schema::create('home_sliders', function (Blueprint $table) {
            $table->id();
            $table->string('title');
            $table->string('subtitle')->nullable();
            $table->string('image');
            $table->string('button_text', 100)->nullable();
            $table->string('button_url')->nullable();
            $table->integer('sort_order')->default(0);
            $table->boolean('status')->default(true);
            $table->timestamps();
        });

        // 2. News & Announcements
        Schema::create('news', function (Blueprint $table) {
            $table->id();
            $table->string('title');
            $table->string('slug')->unique();
            $table->string('image')->nullable();
            $table->date('published_date')->nullable();
            $table->text('summary')->nullable();
            $table->longText('content')->nullable();
            $table->string('url')->nullable();
            $table->boolean('status')->default(true);
            $table->timestamps();
        });

        // 3. Events
        Schema::create('events', function (Blueprint $table) {
            $table->id();
            $table->string('title');
            $table->string('slug')->unique();
            $table->string('image')->nullable();
            $table->date('event_date')->nullable();
            $table->string('event_time', 50)->nullable();
            $table->string('location')->nullable();
            $table->text('description')->nullable();
            $table->string('url')->nullable();
            $table->boolean('status')->default(true);
            $table->timestamps();
        });

        // 4. Galleries
        Schema::create('galleries', function (Blueprint $table) {
            $table->id();
            $table->string('title')->nullable();
            $table->string('category', 100)->default('General');
            $table->string('image');
            $table->integer('sort_order')->default(0);
            $table->boolean('status')->default(true);
            $table->timestamps();
        });

        // 5. Testimonials
        Schema::create('testimonials', function (Blueprint $table) {
            $table->id();
            $table->string('name', 150);
            $table->string('role', 100)->nullable();
            $table->string('image')->nullable();
            $table->text('message');
            $table->tinyInteger('rating')->default(5);
            $table->integer('sort_order')->default(0);
            $table->boolean('status')->default(true);
            $table->timestamps();
        });

        // 6. Website Settings
        Schema::create('website_settings', function (Blueprint $table) {
            $table->id();
            $table->string('key', 100)->unique();
            $table->longText('value')->nullable();
            $table->string('group', 50)->default('general');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('website_settings');
        Schema::dropIfExists('testimonials');
        Schema::dropIfExists('galleries');
        Schema::dropIfExists('events');
        Schema::dropIfExists('news');
        Schema::dropIfExists('home_sliders');
    }
};
