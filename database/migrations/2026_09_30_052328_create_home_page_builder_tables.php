<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        // 1. Home Page Sections
        Schema::create('home_page_sections', function (Blueprint $table) {
            $table->id();
            $table->string('section_key', 100)->unique();
            $table->string('section_type', 50)->index();
            $table->string('title')->nullable();
            $table->string('subtitle')->nullable();
            $table->boolean('is_enabled')->default(true)->index();
            $table->integer('display_order')->default(0)->index();
            $table->string('layout_key', 100);
            $table->string('custom_class')->nullable();
            $table->json('settings')->nullable();
            $table->boolean('is_system')->default(false);
            $table->timestamps();
        });

        // 2. Important Messages
        Schema::create('important_messages', function (Blueprint $table) {
            $table->id();
            $table->string('title');
            $table->text('message');
            $table->string('type', 30)->default('info'); // info, warning, danger, success
            $table->string('action_text', 100)->nullable();
            $table->string('action_url')->nullable();
            $table->dateTime('start_date')->nullable();
            $table->dateTime('end_date')->nullable();
            $table->boolean('status')->default(true)->index();
            $table->integer('sort_order')->default(0);
            $table->timestamps();
        });

        // 3. Announcements
        Schema::create('announcements', function (Blueprint $table) {
            $table->id();
            $table->string('title');
            $table->text('content')->nullable();
            $table->string('badge', 50)->nullable(); // 'Urgent', 'New', 'Notice', etc.
            $table->string('link_url')->nullable();
            $table->string('link_text', 100)->nullable();
            $table->dateTime('start_date')->nullable();
            $table->dateTime('end_date')->nullable();
            $table->boolean('status')->default(true)->index();
            $table->integer('sort_order')->default(0);
            $table->timestamps();
        });

        // Seed default sections with current homepage order and sensible defaults
        $defaultSections = [
            [
                'section_key' => 'hero_slider',
                'section_type' => 'hero_slider',
                'title' => 'Hero Slider',
                'subtitle' => 'Main homepage banner carousel',
                'is_enabled' => true,
                'display_order' => 1,
                'layout_key' => 'hero_slider_01',
                'is_system' => true,
                'settings' => json_encode([]),
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'section_key' => 'important_message',
                'section_type' => 'important_message',
                'title' => 'Important Message',
                'subtitle' => 'Alerts and urgent institute notices',
                'is_enabled' => true,
                'display_order' => 2,
                'layout_key' => 'important_message_01',
                'is_system' => true,
                'settings' => json_encode([]),
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'section_key' => 'announcement',
                'section_type' => 'announcement',
                'title' => 'Announcements',
                'subtitle' => 'Latest news ticker and updates',
                'is_enabled' => true,
                'display_order' => 3,
                'layout_key' => 'announcement_01',
                'is_system' => true,
                'settings' => json_encode(['limit' => 5]),
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'section_key' => 'quick_links',
                'section_type' => 'quick_links',
                'title' => 'Quick Links',
                'subtitle' => 'Fast access to key portals',
                'is_enabled' => true,
                'display_order' => 4,
                'layout_key' => 'quick_links_01',
                'is_system' => true,
                'settings' => json_encode([]),
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'section_key' => 'about',
                'section_type' => 'about',
                'title' => 'About Our School',
                'subtitle' => 'Learn Today, Lead Tomorrow',
                'is_enabled' => true,
                'display_order' => 5,
                'layout_key' => 'about_01',
                'is_system' => true,
                'settings' => json_encode([]),
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'section_key' => 'features',
                'section_type' => 'features',
                'title' => 'Our Core Features',
                'subtitle' => 'Why choose our institution',
                'is_enabled' => true,
                'display_order' => 6,
                'layout_key' => 'features_01',
                'is_system' => true,
                'settings' => json_encode([]),
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'section_key' => 'courses',
                'section_type' => 'courses',
                'title' => 'Our Classes & Programs',
                'subtitle' => 'Academic curricula from Nursery to Senior Secondary',
                'is_enabled' => true,
                'display_order' => 7,
                'layout_key' => 'courses_01',
                'is_system' => true,
                'settings' => json_encode([]),
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'section_key' => 'statistics',
                'section_type' => 'statistics',
                'title' => 'Institute by Numbers',
                'subtitle' => 'Our achievements and community strength',
                'is_enabled' => true,
                'display_order' => 8,
                'layout_key' => 'statistics_01',
                'is_system' => true,
                'settings' => json_encode([]),
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'section_key' => 'news',
                'section_type' => 'news',
                'title' => 'Latest News & Notices',
                'subtitle' => 'Stay informed with our latest announcements',
                'is_enabled' => true,
                'display_order' => 9,
                'layout_key' => 'news_01',
                'is_system' => true,
                'settings' => json_encode(['limit' => 3]),
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'section_key' => 'events',
                'section_type' => 'events',
                'title' => 'Upcoming Events',
                'subtitle' => 'Join us in celebrating school moments',
                'is_enabled' => true,
                'display_order' => 10,
                'layout_key' => 'events_01',
                'is_system' => true,
                'settings' => json_encode(['limit' => 3, 'filter' => 'upcoming']),
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'section_key' => 'principal',
                'section_type' => 'principal',
                'title' => 'Principal\'s Desk',
                'subtitle' => 'Message from our leadership',
                'is_enabled' => true,
                'display_order' => 11,
                'layout_key' => 'principal_01',
                'is_system' => true,
                'settings' => json_encode([]),
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'section_key' => 'gallery',
                'section_type' => 'gallery',
                'title' => 'Photo Gallery',
                'subtitle' => 'Capturing campus life and special moments',
                'is_enabled' => true,
                'display_order' => 12,
                'layout_key' => 'gallery_01',
                'is_system' => true,
                'settings' => json_encode(['limit' => 6, 'category' => 'all']),
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'section_key' => 'testimonials',
                'section_type' => 'testimonials',
                'title' => 'Parent & Student Feedback',
                'subtitle' => 'What our school community says',
                'is_enabled' => true,
                'display_order' => 13,
                'layout_key' => 'testimonials_01',
                'is_system' => true,
                'settings' => json_encode([]),
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'section_key' => 'contact',
                'section_type' => 'contact',
                'title' => 'Get In Touch',
                'subtitle' => 'Reach out to our administration office',
                'is_enabled' => true,
                'display_order' => 14,
                'layout_key' => 'contact_01',
                'is_system' => true,
                'settings' => json_encode([]),
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'section_key' => 'admission_enquiry',
                'section_type' => 'admission_enquiry',
                'title' => 'Admission Enquiry',
                'subtitle' => 'Apply online for registration',
                'is_enabled' => true,
                'display_order' => 15,
                'layout_key' => 'admission_enquiry_01',
                'is_system' => true,
                'settings' => json_encode([]),
                'created_at' => now(),
                'updated_at' => now(),
            ],
        ];

        DB::table('home_page_sections')->insert($defaultSections);

        // Seed sample Announcement & Important Message
        DB::table('announcements')->insert([
            [
                'title' => 'Admissions Open for Academic Session 2026-27 (Nursery to Class 11)',
                'content' => 'Online registrations and campus walk-in admissions are now open.',
                'badge' => 'Admission',
                'link_url' => url('/admission'),
                'link_text' => 'Apply Now',
                'status' => true,
                'sort_order' => 1,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'title' => 'Annual Science Exhibition and Inter-School Competition Scheduled on 15th next month',
                'content' => 'Parents and students are cordially invited to participate.',
                'badge' => 'Event',
                'link_url' => url('/events'),
                'link_text' => 'View Details',
                'status' => true,
                'sort_order' => 2,
                'created_at' => now(),
                'updated_at' => now(),
            ],
        ]);

        DB::table('important_messages')->insert([
            [
                'title' => 'Important Notice: School Timings During Winter / Exam Season',
                'message' => 'Please note that morning school assembly will commence at 08:30 AM starting Monday. All students must report in full uniform.',
                'type' => 'warning',
                'action_text' => 'Read Circular',
                'action_url' => url('/news'),
                'status' => true,
                'sort_order' => 1,
                'created_at' => now(),
                'updated_at' => now(),
            ],
        ]);
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('announcements');
        Schema::dropIfExists('important_messages');
        Schema::dropIfExists('home_page_sections');
    }
};
