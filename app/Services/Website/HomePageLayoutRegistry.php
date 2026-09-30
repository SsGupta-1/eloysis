<?php

namespace App\Services\Website;

class HomePageLayoutRegistry
{
    /**
     * Section type definitions and their supported layout variants.
     */
    protected static array $registry = [
        'hero_slider' => [
            'name' => 'Hero Slider',
            'description' => 'Dynamic carousel displaying slides with headlines, buttons, and high-impact imagery.',
            'icon' => 'bi-images',
            'is_repeatable' => false,
            'default_layout' => 'hero_slider_01',
            'layouts' => [
                'hero_slider_01' => [
                    'name' => 'Full-Width Classic Slider',
                    'description' => 'Full-width banner slider with animated text captions, CTA buttons, and slide controls.',
                    'preview_badge' => 'Classic',
                ],
                'hero_slider_02' => [
                    'name' => 'Split Grid Hero Banner',
                    'description' => 'Left-side headline card with key admissions highlights & right-side slide showcase.',
                    'preview_badge' => 'Split',
                ],
                'hero_slider_03' => [
                    'name' => 'Glassmorphism Centered Hero',
                    'description' => 'High-impact background slider with translucent floating content card and fast actions.',
                    'preview_badge' => 'Glass',
                ],
            ],
        ],

        'important_message' => [
            'name' => 'Important Message',
            'description' => 'Prominent alert notices, urgent announcements, or circulars for parents & students.',
            'icon' => 'bi-exclamation-triangle-fill',
            'is_repeatable' => false,
            'default_layout' => 'important_message_01',
            'layouts' => [
                'important_message_01' => [
                    'name' => 'Urgent Alert Banner',
                    'description' => 'Full-width colored alert ribbon with icon, marquee message, and direct circular button.',
                    'preview_badge' => 'Ribbon',
                ],
                'important_message_02' => [
                    'name' => 'Callout Card Box',
                    'description' => 'Framed emergency/urgent card box with badge, timestamp, and action button.',
                    'preview_badge' => 'Card',
                ],
                'important_message_03' => [
                    'name' => 'Spotlight Notice Board',
                    'description' => 'Prominent centered highlight box with bold header and detailed paragraph alert.',
                    'preview_badge' => 'Spotlight',
                ],
            ],
        ],

        'announcement' => [
            'name' => 'Announcements',
            'description' => 'Latest school announcements, news bulletins, and live updates ticker.',
            'icon' => 'bi-megaphone-fill',
            'is_repeatable' => false,
            'default_layout' => 'announcement_01',
            'layouts' => [
                'announcement_01' => [
                    'name' => 'Live Scrolling Ticker',
                    'description' => 'Smooth horizontal news ticker bar displaying latest announcement items with badges.',
                    'preview_badge' => 'Ticker',
                ],
                'announcement_02' => [
                    'name' => 'Announcement Grid Cards',
                    'description' => '3-column cards grid displaying latest notices with publish dates and direct links.',
                    'preview_badge' => 'Grid',
                ],
                'announcement_03' => [
                    'name' => 'Notice Board List',
                    'description' => 'Clean vertical bulletin board list with date icons and read more links.',
                    'preview_badge' => 'List',
                ],
            ],
        ],

        'events' => [
            'name' => 'Events',
            'description' => 'Upcoming sports days, academic fairs, examinations, and cultural festivals.',
            'icon' => 'bi-calendar-event',
            'is_repeatable' => false,
            'default_layout' => 'events_01',
            'layouts' => [
                'events_01' => [
                    'name' => 'Event Cards Grid',
                    'description' => '3-column cards with calendar date badges, time, location, and details.',
                    'preview_badge' => 'Grid',
                ],
                'events_02' => [
                    'name' => 'Event Timeline / List',
                    'description' => 'Modern vertical timeline view showing chronological upcoming school events.',
                    'preview_badge' => 'Timeline',
                ],
                'events_03' => [
                    'name' => 'Featured Event Spotlight',
                    'description' => 'Large highlighted next event banner on left with side list of upcoming events.',
                    'preview_badge' => 'Featured',
                ],
            ],
        ],

        'gallery' => [
            'name' => 'Photo Gallery',
            'description' => 'Showcase of school campus, lab infrastructure, classrooms, and activities.',
            'icon' => 'bi-camera-fill',
            'is_repeatable' => false,
            'default_layout' => 'gallery_01',
            'layouts' => [
                'gallery_01' => [
                    'name' => 'Standard Image Grid',
                    'description' => '3-column responsive photo grid with hover zoom captions.',
                    'preview_badge' => 'Grid',
                ],
                'gallery_02' => [
                    'name' => 'Category Tabbed Showcase',
                    'description' => 'Filterable category tabs (Campus, Sports, Academics, Events) with animated gallery.',
                    'preview_badge' => 'Filterable',
                ],
                'gallery_03' => [
                    'name' => 'Masonry Showcase',
                    'description' => 'Modern dynamic height masonry gallery layout for visual impact.',
                    'preview_badge' => 'Masonry',
                ],
            ],
        ],

        'custom_content' => [
            'name' => 'Custom Content Section',
            'description' => 'Fully customizable block for custom text, images, CTA buttons, or special promotions.',
            'icon' => 'bi-layout-text-window-reverse',
            'is_repeatable' => true,
            'default_layout' => 'custom_content_01',
            'layouts' => [
                'custom_content_01' => [
                    'name' => 'Two-Column Split (Content + Image)',
                    'description' => 'Heading, paragraph description, and CTA button on one side with image on the other.',
                    'preview_badge' => 'Split',
                ],
                'custom_content_02' => [
                    'name' => 'Full-Width Callout Banner',
                    'description' => 'Centered bold statement with custom background color/image and primary action button.',
                    'preview_badge' => 'Banner',
                ],
                'custom_content_03' => [
                    'name' => 'Feature Cards Box',
                    'description' => 'Centered section with custom rich text block and framed highlight box.',
                    'preview_badge' => 'Box',
                ],
            ],
        ],

        'quick_links' => [
            'name' => 'Quick Links',
            'description' => 'Fast navigation tiles to key portals (Admissions, Online Exam, Results).',
            'icon' => 'bi-link-45deg',
            'is_repeatable' => false,
            'default_layout' => 'quick_links_01',
            'layouts' => [
                'quick_links_01' => [
                    'name' => 'Floating Action Cards',
                    'description' => '3 floating cards hovering below hero slider with icons and color accents.',
                    'preview_badge' => 'Cards',
                ],
            ],
        ],

        'about' => [
            'name' => 'About Us',
            'description' => 'School introduction, vision, mission, and leadership overview.',
            'icon' => 'bi-info-circle-fill',
            'is_repeatable' => false,
            'default_layout' => 'about_01',
            'layouts' => [
                'about_01' => [
                    'name' => 'Standard Split About Section',
                    'description' => 'Left text with mission details & right school image showcase.',
                    'preview_badge' => 'Split',
                ],
            ],
        ],

        'features' => [
            'name' => 'Features / Highlights',
            'description' => 'Key strengths (Expert Faculty, Smart Classes, Digital Library, Sports).',
            'icon' => 'bi-star-fill',
            'is_repeatable' => false,
            'default_layout' => 'features_01',
            'layouts' => [
                'features_01' => [
                    'name' => '6-Grid Feature Cards',
                    'description' => 'Interactive 6-card grid with icons and brief descriptions.',
                    'preview_badge' => 'Grid',
                ],
            ],
        ],

        'courses' => [
            'name' => 'Classes & Curriculum',
            'description' => 'Overview of classes and academic programs offered.',
            'icon' => 'bi-book-fill',
            'is_repeatable' => false,
            'default_layout' => 'courses_01',
            'layouts' => [
                'courses_01' => [
                    'name' => 'Class Pills / Cards',
                    'description' => 'Grid of available grades/classes with student capacity info.',
                    'preview_badge' => 'Cards',
                ],
            ],
        ],

        'statistics' => [
            'name' => 'Statistics & Counters',
            'description' => 'Visual counters for students, teachers, pass rates, and achievements.',
            'icon' => 'bi-bar-chart-fill',
            'is_repeatable' => false,
            'default_layout' => 'statistics_01',
            'layouts' => [
                'statistics_01' => [
                    'name' => '4-Column Counter Bar',
                    'description' => 'High-contrast counter ribbon with big numbers and icons.',
                    'preview_badge' => 'Counter',
                ],
            ],
        ],

        'news' => [
            'name' => 'News & Notices',
            'description' => 'Official press releases, news articles, and examination circulars.',
            'icon' => 'bi-newspaper',
            'is_repeatable' => false,
            'default_layout' => 'news_01',
            'layouts' => [
                'news_01' => [
                    'name' => '3-Card News Grid',
                    'description' => 'Cards with cover image, publish date badge, excerpt, and full story link.',
                    'preview_badge' => 'Grid',
                ],
            ],
        ],

        'principal' => [
            'name' => 'Principal\'s Desk',
            'description' => 'Personal message from the principal with photograph and signature.',
            'icon' => 'bi-person-badge-fill',
            'is_repeatable' => false,
            'default_layout' => 'principal_01',
            'layouts' => [
                'principal_01' => [
                    'name' => 'Classic Principal Desk',
                    'description' => 'Photo card with quote, detailed leadership message, and signature.',
                    'preview_badge' => 'Classic',
                ],
            ],
        ],

        'testimonials' => [
            'name' => 'Testimonials',
            'description' => 'Feedback and reviews from parents, students, and alumni.',
            'icon' => 'bi-chat-square-quote-fill',
            'is_repeatable' => false,
            'default_layout' => 'testimonials_01',
            'layouts' => [
                'testimonials_01' => [
                    'name' => '3-Card Testimonials Grid',
                    'description' => 'Quotes with 5-star ratings, reviewer name, role, and avatar.',
                    'preview_badge' => 'Cards',
                ],
            ],
        ],

        'contact' => [
            'name' => 'Contact Section',
            'description' => 'Direct contact form, Google Map location, and contact details.',
            'icon' => 'bi-envelope-fill',
            'is_repeatable' => false,
            'default_layout' => 'contact_01',
            'layouts' => [
                'contact_01' => [
                    'name' => 'Contact Form with Info & Map',
                    'description' => 'Left contact details + right interactive message submission form.',
                    'preview_badge' => 'Split',
                ],
            ],
        ],

        'admission_enquiry' => [
            'name' => 'Admission Enquiry',
            'description' => 'Online student admission registration and lead capture form.',
            'icon' => 'bi-mortarboard-fill',
            'is_repeatable' => false,
            'default_layout' => 'admission_enquiry_01',
            'layouts' => [
                'admission_enquiry_01' => [
                    'name' => 'Admission Application Form',
                    'description' => 'Structured enquiry form with class selection, parent details, and reference options.',
                    'preview_badge' => 'Form',
                ],
            ],
        ],
    ];

    /**
     * Get all registered section types with their metadata and layouts.
     */
    public static function getAll(): array
    {
        return self::$registry;
    }

    /**
     * Alias for getAll().
     */
    public static function all(): array
    {
        return self::$registry;
    }

    /**
     * Get details for a specific section type.
     */
    public static function getSectionType(string $type): ?array
    {
        return self::$registry[$type] ?? null;
    }

    /**
     * Get all layouts available for a section type.
     */
    public static function getLayoutsForType(string $type): array
    {
        return self::$registry[$type]['layouts'] ?? [];
    }

    /**
     * Get default layout for a section type.
     */
    public static function getDefaultLayout(string $type): string
    {
        return self::$registry[$type]['default_layout'] ?? ($type.'_01');
    }

    /**
     * Check if a layout is valid for the given section type.
     */
    public static function isValidLayout(string $type, string $layoutKey): bool
    {
        return isset(self::$registry[$type]['layouts'][$layoutKey]);
    }

    /**
     * Resolve a layout key safely with fallback to default layout.
     */
    public static function resolveLayout(string $type, ?string $layoutKey): string
    {
        if (! empty($layoutKey) && self::isValidLayout($type, $layoutKey)) {
            return $layoutKey;
        }

        return self::getDefaultLayout($type);
    }
}
