<?php

use App\Models\Announcement;
use App\Models\HomePageSection;
use App\Models\ImportantMessage;
use App\Models\QuickLink;
use App\Models\Role;
use App\Models\Testimonial;
use App\Models\User;
use App\Services\Website\HomePageLayoutRegistry;
use Illuminate\Support\Facades\Hash;

beforeEach(function () {
    $this->adminRole = Role::firstOrCreate(
        ['slug' => 'super_admin'],
        ['role_name' => 'Super Admin', 'status' => 1]
    );

    $this->adminUser = User::firstOrCreate(
        ['email' => 'builder_admin@test.com'],
        [
            'name' => 'Builder Admin',
            'mobile' => '9999888811',
            'password' => Hash::make('password'),
            'role_id' => $this->adminRole->id,
            'status' => 1,
        ]
    );
});

test('unauthenticated guest cannot access home page builder admin endpoints', function () {
    $this->get(route('admin.homepage-builder.index'))
        ->assertRedirect(route('admin.login'));

    $this->post(route('admin.homepage-builder.orders'), ['orders' => []])
        ->assertRedirect(route('admin.login'));
});

test('admin can access home page builder dashboard', function () {
    $this->actingAs($this->adminUser, 'admin')
        ->get(route('admin.homepage-builder.index'))
        ->assertSuccessful()
        ->assertSee('Home Page Builder')
        ->assertSee('Hero Slider')
        ->assertSee('Events')
        ->assertSee('Gallery');
});

test('admin can toggle section enabled and disabled status', function () {
    $section = HomePageSection::firstOrCreate(
        ['section_type' => 'events'],
        [
            'title' => 'Upcoming Events',
            'layout_key' => 'events_01',
            'is_enabled' => true,
            'display_order' => 5,
            'is_system' => true,
        ]
    );

    $response = $this->actingAs($this->adminUser, 'admin')
        ->patchJson(route('admin.homepage-builder.status', $section->id), [
            'is_enabled' => false,
        ]);

    $response->assertSuccessful()
        ->assertJsonPath('status', true);

    expect($section->fresh()->is_enabled)->toBeFalse();
});

test('admin can change section layout variant', function () {
    $section = HomePageSection::firstOrCreate(
        ['section_type' => 'events'],
        [
            'title' => 'Upcoming Events',
            'layout_key' => 'events_01',
            'is_enabled' => true,
            'display_order' => 5,
            'is_system' => true,
        ]
    );

    $response = $this->actingAs($this->adminUser, 'admin')
        ->patchJson(route('admin.homepage-builder.layout', $section->id), [
            'layout_key' => 'events_02',
        ]);

    $response->assertSuccessful()
        ->assertJsonPath('status', true);

    expect($section->fresh()->layout_key)->toBe('events_02');
});

test('admin can reorder homepage sections', function () {
    $section1 = HomePageSection::where('section_type', 'hero_slider')->first();
    $section2 = HomePageSection::where('section_type', 'events')->first();

    if (! $section1 || ! $section2) {
        $section1 = HomePageSection::create([
            'section_type' => 'hero_slider',
            'title' => 'Slider',
            'layout_key' => 'hero_slider_01',
            'display_order' => 1,
            'is_enabled' => true,
        ]);
        $section2 = HomePageSection::create([
            'section_type' => 'events',
            'title' => 'Events',
            'layout_key' => 'events_01',
            'display_order' => 2,
            'is_enabled' => true,
        ]);
    }

    $response = $this->actingAs($this->adminUser, 'admin')
        ->postJson(route('admin.homepage-builder.orders'), [
            'orders' => [
                ['id' => $section2->id, 'order' => 1],
                ['id' => $section1->id, 'order' => 2],
            ],
        ]);

    $response->assertSuccessful()
        ->assertJsonPath('status', true);

    expect($section2->fresh()->display_order)->toBe(1);
    expect($section1->fresh()->display_order)->toBe(2);
});

test('admin can create, update and delete custom content sections', function () {
    $uniqueTitle = 'Special Spotlight '.uniqid();
    $updatedTitle = 'Updated Spotlight '.uniqid();

    // 1. Create custom section
    $createResponse = $this->actingAs($this->adminUser, 'admin')
        ->postJson(route('admin.homepage-builder.custom-section.store'), [
            'title' => $uniqueTitle,
            'subtitle' => 'Our Excellence in Education',
            'layout_key' => 'custom_content_01',
            'content' => '<p>Leading the way in modern STEM research.</p>',
            'button_text' => 'Discover More',
            'button_url' => 'https://example.com/stem',
            'is_enabled' => 1,
        ]);

    $createResponse->assertSuccessful()
        ->assertJsonPath('status', true);

    $created = HomePageSection::where('title', $uniqueTitle)->first();
    expect($created)->not->toBeNull()
        ->and($created->section_type)->toBe('custom_content')
        ->and($created->settings['content'] ?? '')->toContain('STEM research')
        ->and($created->is_system)->toBeFalse();

    // 2. Update custom section
    $updateResponse = $this->actingAs($this->adminUser, 'admin')
        ->postJson(route('admin.homepage-builder.custom-section.store'), [
            'id' => $created->id,
            'title' => $updatedTitle,
            'subtitle' => 'Global Excellence',
            'layout_key' => 'custom_content_02',
            'content' => '<p>Updated content body</p>',
            'button_text' => 'Read More',
            'button_url' => '/about',
            'is_enabled' => 1,
        ]);

    $updateResponse->assertSuccessful()
        ->assertJsonPath('status', true);

    expect($created->fresh()->title)->toBe($updatedTitle)
        ->and($created->fresh()->layout_key)->toBe('custom_content_02');

    // 3. Delete custom section
    $deleteResponse = $this->actingAs($this->adminUser, 'admin')
        ->deleteJson(route('admin.homepage-builder.custom-section.destroy', $created->id));

    $deleteResponse->assertSuccessful()
        ->assertJsonPath('status', true);

    expect(HomePageSection::find($created->id))->toBeNull();
});

test('admin cannot delete system sections', function () {
    $systemSection = HomePageSection::where('is_system', true)->first();
    expect($systemSection)->not->toBeNull();

    $response = $this->actingAs($this->adminUser, 'admin')
        ->deleteJson(route('admin.homepage-builder.custom-section.destroy', $systemSection->id));

    $response->assertStatus(400)
        ->assertJsonPath('status', false);

    expect(HomePageSection::find($systemSection->id))->not->toBeNull();
});

test('admin can manage important messages and announcements', function () {
    // 1. Create Important Message
    $msgResponse = $this->actingAs($this->adminUser, 'admin')
        ->postJson(route('admin.homepage-builder.messages.store'), [
            'badge' => 'Urgent Alert',
            'title' => 'Campus Maintenance on Sunday',
            'message' => 'Online portal will undergo scheduled maintenance from 2 AM to 6 AM.',
            'type' => 'warning',
            'cta_text' => 'Helpdesk',
            'cta_url' => '/contact',
            'is_active' => 1,
        ]);

    $msgResponse->assertSuccessful()
        ->assertJsonPath('status', true);

    $message = ImportantMessage::where('title', 'Campus Maintenance on Sunday')->first();
    expect($message)->not->toBeNull();

    // 2. Create Announcement
    $annResponse = $this->actingAs($this->adminUser, 'admin')
        ->postJson(route('admin.homepage-builder.announcements.store'), [
            'badge' => 'New',
            'title' => 'Admissions Open for Session 2026-27',
            'content' => 'Applications are now invited for all undergraduate courses.',
            'link_text' => 'Apply Online',
            'link_url' => '/admission-enquiry',
            'is_active' => 1,
        ]);

    $annResponse->assertSuccessful()
        ->assertJsonPath('status', true);

    $announcement = Announcement::where('title', 'Admissions Open for Session 2026-27')->first();
    expect($announcement)->not->toBeNull();

    // 3. Create & Delete Testimonial
    $testiResponse = $this->actingAs($this->adminUser, 'admin')
        ->postJson(route('admin.homepage-builder.testimonials.store'), [
            'name' => 'Dr. Sunita Rao',
            'role' => 'Parent of Class 10th Student',
            'message' => 'The digital campus infrastructure and dedicated mentors are unmatched.',
            'rating' => 5,
            'is_active' => 1,
        ]);

    $testiResponse->assertSuccessful()
        ->assertJsonPath('status', true);

    $testi = Testimonial::where('name', 'Dr. Sunita Rao')->first();
    expect($testi)->not->toBeNull();

    $this->actingAs($this->adminUser, 'admin')
        ->deleteJson(route('admin.homepage-builder.testimonials.destroy', $testi->id))
        ->assertSuccessful();

    // 4. Create & Delete Quick Link
    $qlResponse = $this->actingAs($this->adminUser, 'admin')
        ->postJson(route('admin.homepage-builder.quick-links.store'), [
            'title' => 'Admissions 2026 Portal',
            'description' => 'Fast-track your application',
            'url' => '/admission-enquiry',
            'icon' => 'bi-mortarboard-fill',
            'color' => 'primary',
            'is_active' => 1,
        ]);

    $qlResponse->assertSuccessful()
        ->assertJsonPath('status', true);

    $ql = QuickLink::where('title', 'Admissions 2026 Portal')->first();
    expect($ql)->not->toBeNull();

    $this->actingAs($this->adminUser, 'admin')
        ->deleteJson(route('admin.homepage-builder.quick-links.destroy', $ql->id))
        ->assertSuccessful();
});

test('public homepage renders dynamically ordered sections and respects visibility', function () {
    // Ensure hero slider is enabled and has order 1
    $slider = HomePageSection::firstOrCreate(
        ['section_type' => 'hero_slider'],
        ['title' => 'Hero Slider', 'layout_key' => 'hero_slider_01', 'is_enabled' => true, 'display_order' => 1, 'is_system' => true]
    );
    $slider->update(['is_enabled' => true, 'display_order' => 1, 'layout_key' => 'hero_slider_01']);

    // Create a custom section with unique content
    $custom = HomePageSection::create([
        'section_type' => 'custom_content',
        'title' => 'Dynamic Verification Header 2026',
        'subtitle' => 'Unique Subtitle For Pest Test',
        'layout_key' => 'custom_content_01',
        'is_enabled' => true,
        'display_order' => 2,
        'is_system' => false,
        'settings' => [
            'content' => 'Dynamic Unique Test Content Text 12345',
            'button_text' => 'Click Test',
            'button_url' => '#test',
        ],
    ]);

    // Disable events section
    $events = HomePageSection::where('section_type', 'events')->first();
    if ($events) {
        $events->update(['is_enabled' => false]);
    }

    $response = $this->get(route('home'));

    $response->assertSuccessful()
        ->assertSee('Dynamic Verification Header 2026')
        ->assertSee('Dynamic Unique Test Content Text 12345');

    // Clean up
    $custom->delete();
});

test('layout registry provides valid fallback if layout is unknown', function () {
    $fallback = HomePageLayoutRegistry::resolveLayout('events', 'events_unknown_999');
    expect($fallback)->toBe('events_01');

    $allLayouts = HomePageLayoutRegistry::all();
    expect($allLayouts)->toHaveKey('hero_slider')
        ->and($allLayouts)->toHaveKey('events')
        ->and($allLayouts)->toHaveKey('gallery')
        ->and($allLayouts)->toHaveKey('important_message')
        ->and($allLayouts)->toHaveKey('announcement')
        ->and($allLayouts)->toHaveKey('custom_content');
});
