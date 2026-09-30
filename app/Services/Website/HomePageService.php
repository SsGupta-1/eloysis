<?php

namespace App\Services\Website;

use App\Models\AcademicClass;
use App\Models\Event;
use App\Models\Gallery;
use App\Models\HomeSlider;
use App\Models\News;
use App\Models\Testimonial;
use App\Models\WebsiteSetting;

class HomePageService
{
    /**
     * Get Home Page Data
     */
    public function getPageData(): array
    {
        return [
            'institute' => $this->getInstitute(),
            'sliders' => $this->getSliders(),
            'quick_links' => $this->getQuickLinks(),
            'about' => $this->getAbout(),
            'features' => $this->getFeatures(),
            'classes' => $this->getClasses(),
            'statistics' => $this->getStatistics(),
            'news' => $this->getNews(),
            'events' => $this->getEvents(),
            'principal' => $this->getPrincipal(),
            'gallery' => $this->getGallery(),
            'footer' => $this->getFooter(),
            'testimonials' => $this->getTestimonials(),
            'contact' => $this->getContact(),
        ];
    }

    private function getInstitute(): array
    {
        $logo = WebsiteSetting::get('institute_logo');
        $logoUrl = $logo ? asset('storage/'.$logo) : asset('assets/website/images/logo.png');

        return [
            'name' => WebsiteSetting::get('institute_name', 'ABC Public School'),
            'tagline' => WebsiteSetting::get('institute_tagline', 'Learn Today, Lead Tomorrow'),
            'logo' => $logoUrl,
        ];
    }

    private function getSliders(): array
    {
        $dbSliders = HomeSlider::where('status', true)->orderBy('sort_order', 'asc')->get();

        if ($dbSliders->isNotEmpty()) {
            return $dbSliders->map(fn ($slider) => [
                'title' => $slider->title,
                'subtitle' => $slider->subtitle,
                'image' => $slider->image_url,
                'button_text' => $slider->button_text,
                'button_url' => $slider->button_url ?? '#',
            ])->toArray();
        }

        return [
            [
                'title' => 'Welcome To ABC Public School',
                'subtitle' => 'Empowering Students Through Quality Education',
                'image' => asset('assets/website/images/slider/slider-1.png'),
                'button_text' => 'Admission Open',
                'button_url' => '#admission-enquiry',
            ],
            [
                'title' => 'Online Examination System',
                'subtitle' => 'Smart, Secure and Digital Examination Platform',
                'image' => asset('assets/website/images/slider/slider-2.png'),
                'button_text' => 'Explore',
                'button_url' => '#',
            ],
        ];
    }

    private function getQuickLinks(): array
    {
        return [
            [
                'title' => 'Admission Open',
                'description' => 'Apply for new admission.',
                'icon' => 'bi bi-mortarboard-fill',
                'url' => route('admission'),
                'color' => 'primary',
            ],
            [
                'title' => 'Online Exam',
                'description' => 'Start your online examination.',
                'icon' => 'bi bi-laptop',
                'url' => route('admin.login'),
                'color' => 'success',
            ],
            [
                'title' => 'Latest Result',
                'description' => 'Check examination results.',
                'icon' => 'bi bi-award-fill',
                'url' => '#',
                'color' => 'warning',
            ],
        ];
    }

    private function getAbout(): array
    {
        $aboutImage = WebsiteSetting::get('about_image');
        $imageUrl = $aboutImage ? asset('storage/'.$aboutImage) : asset('assets/website/images/slider/slider-2.png');

        return [
            'title' => WebsiteSetting::get('about_title', 'About Our Institute'),
            'subtitle' => WebsiteSetting::get('about_subtitle', 'Excellence in Education Since 2010'),
            'description' => WebsiteSetting::get('about_description', 'ABC Public School is committed to providing quality education with modern teaching methods, experienced faculty, and a technology-driven learning environment. Our goal is to develop students academically, socially, and morally.'),
            'image' => $imageUrl,
            'button_text' => 'Read More',
            'button_url' => route('about'),
        ];
    }

    private function getFeatures(): array
    {
        return [
            [
                'icon' => 'bi bi-mortarboard-fill',
                'title' => 'Expert Teachers',
                'description' => 'Experienced and qualified faculty members.',
            ],
            [
                'icon' => 'bi bi-laptop',
                'title' => 'Smart Classes',
                'description' => 'Technology-enabled interactive classrooms.',
            ],
            [
                'icon' => 'bi bi-book-fill',
                'title' => 'Digital Library',
                'description' => 'Access to books and digital learning resources.',
            ],
            [
                'icon' => 'bi bi-pencil-square',
                'title' => 'Online Exams',
                'description' => 'Secure and fast online examination system.',
            ],
            [
                'icon' => 'bi bi-award-fill',
                'title' => 'Best Results',
                'description' => 'Excellent academic performance every year.',
            ],
            [
                'icon' => 'bi bi-dribbble',
                'title' => 'Sports Activities',
                'description' => 'Overall development through sports and games.',
            ],
        ];
    }

    private function getClasses(): array
    {
        $dbClasses = AcademicClass::where('status', true)->orderBy('sort_order', 'asc')->get();

        if ($dbClasses->isNotEmpty()) {
            return $dbClasses->map(fn ($c) => [
                'name' => $c->class_name,
                'students' => 40,
            ])->toArray();
        }

        return [
            ['name' => 'Class 1',  'students' => 40],
            ['name' => 'Class 2',  'students' => 42],
            ['name' => 'Class 3',  'students' => 38],
            ['name' => 'Class 4',  'students' => 45],
            ['name' => 'Class 5',  'students' => 41],
            ['name' => 'Class 6',  'students' => 39],
            ['name' => 'Class 7',  'students' => 44],
            ['name' => 'Class 8',  'students' => 40],
            ['name' => 'Class 9',  'students' => 37],
            ['name' => 'Class 10', 'students' => 43],
            ['name' => 'Class 11', 'students' => 35],
            ['name' => 'Class 12', 'students' => 30],
        ];
    }

    private function getStatistics(): array
    {
        return [
            [
                'count' => WebsiteSetting::get('stat_students', '2500+'),
                'title' => 'Students',
                'icon' => 'bi bi-people-fill',
            ],
            [
                'count' => WebsiteSetting::get('stat_teachers', '120+'),
                'title' => 'Teachers',
                'icon' => 'bi bi-person-workspace',
            ],
            [
                'count' => WebsiteSetting::get('stat_classes', '12'),
                'title' => 'Classes',
                'icon' => 'bi bi-building',
            ],
            [
                'count' => WebsiteSetting::get('stat_results', '98%'),
                'title' => 'Result',
                'icon' => 'bi bi-trophy-fill',
            ],
        ];
    }

    private function getNews(): array
    {
        $dbNews = News::where('status', true)->latest('published_date')->take(6)->get();

        if ($dbNews->isNotEmpty()) {
            return $dbNews->map(fn ($item) => [
                'title' => $item->title,
                'image' => $item->image_url,
                'date' => $item->published_date ? $item->published_date->format('d M Y') : date('d M Y'),
                'description' => $item->summary ?? $item->content,
                'url' => $item->url ?? route('news'),
            ])->toArray();
        }

        return [
            [
                'title' => 'Admission Open for Session 2026-27',
                'image' => asset('assets/website/images/slider/slider-1.png'),
                'date' => '26 Jun 2026',
                'description' => 'Admissions are now open for all classes. Apply before the last date.',
                'url' => route('admission'),
            ],
            [
                'title' => 'Annual Sports Day Celebration',
                'image' => asset('assets/website/images/slider/slider-2.png'),
                'date' => '20 Jun 2026',
                'description' => 'Students participated in various sports activities with great enthusiasm.',
                'url' => route('news'),
            ],
            [
                'title' => 'Class 10 Board Result Declared',
                'image' => asset('assets/website/images/slider/slider-1.png'),
                'date' => '15 Jun 2026',
                'description' => 'Congratulations to all students for achieving excellent results.',
                'url' => route('news'),
            ],
        ];
    }

    private function getEvents(): array
    {
        $dbEvents = Event::where('status', true)->latest('event_date')->take(6)->get();

        if ($dbEvents->isNotEmpty()) {
            return $dbEvents->map(fn ($ev) => [
                'date' => $ev->event_date ? $ev->event_date->format('d') : '01',
                'month' => $ev->event_date ? strtoupper($ev->event_date->format('M')) : 'JAN',
                'title' => $ev->title,
                'time' => $ev->event_time ?? '09:00 AM',
                'location' => $ev->location ?? 'School Campus',
                'url' => $ev->url ?? route('events'),
            ])->toArray();
        }

        return [
            [
                'date' => '05',
                'month' => 'JUL',
                'title' => 'Science Exhibition',
                'time' => '10:00 AM',
                'location' => 'School Campus',
                'url' => route('events'),
            ],
            [
                'date' => '15',
                'month' => 'JUL',
                'title' => 'Parents Teacher Meeting',
                'time' => '09:30 AM',
                'location' => 'Conference Hall',
                'url' => route('events'),
            ],
            [
                'date' => '25',
                'month' => 'JUL',
                'title' => 'Annual Sports Competition',
                'time' => '08:00 AM',
                'location' => 'Play Ground',
                'url' => route('events'),
            ],
        ];
    }

    private function getPrincipal(): array
    {
        $photo = WebsiteSetting::get('principal_image');
        $photoUrl = $photo ? asset('storage/'.$photo) : asset('assets/website/images/slider/slider-1.png');

        $sig = WebsiteSetting::get('principal_signature');
        $sigUrl = $sig ? asset('storage/'.$sig) : null;

        return [
            'name' => WebsiteSetting::get('principal_name', 'Dr. Rajesh Kumar'),
            'designation' => WebsiteSetting::get('principal_designation', 'Principal'),
            'image' => $photoUrl,
            'message' => WebsiteSetting::get('principal_message', 'Welcome to ABC Public School. Our mission is to provide quality education that inspires students to become responsible citizens and lifelong learners. We focus on academic excellence, discipline, innovation, and overall personality development.'),
            'signature' => $sigUrl,
        ];
    }

    private function getGallery(): array
    {
        $dbGalleries = Gallery::where('status', true)->orderBy('sort_order', 'asc')->take(12)->get();

        if ($dbGalleries->isNotEmpty()) {
            return $dbGalleries->map(fn ($g) => [
                'title' => $g->title,
                'category' => $g->category,
                'image' => $g->image_url,
            ])->toArray();
        }

        return [
            ['image' => asset('assets/website/images/slider/slider-1.png'), 'title' => 'Campus', 'category' => 'Campus'],
            ['image' => asset('assets/website/images/slider/slider-2.png'), 'title' => 'Computer Lab', 'category' => 'Academics'],
            ['image' => asset('assets/website/images/slider/slider-1.png'), 'title' => 'Annual Function', 'category' => 'Events'],
            ['image' => asset('assets/website/images/slider/slider-2.png'), 'title' => 'Playground', 'category' => 'Sports'],
            ['image' => asset('assets/website/images/slider/slider-1.png'), 'title' => 'Science Exhibition', 'category' => 'Events'],
            ['image' => asset('assets/website/images/slider/slider-2.png'), 'title' => 'Library', 'category' => 'Academics'],
        ];
    }

    private function getFooter(): array
    {
        $name = WebsiteSetting::get('institute_name', 'ABC Public School');
        $about = WebsiteSetting::get('footer_about', 'ABC Public School is committed to providing quality education with modern learning methods and overall student development.');
        $copyright = WebsiteSetting::get('footer_copyright', '© '.date('Y').' ABC Public School. All Rights Reserved.');

        return [
            'about' => [
                'title' => $name,
                'description' => $about,
            ],
            'quick_links' => [
                ['title' => 'Home', 'url' => route('home')],
                ['title' => 'About Us', 'url' => route('about')],
                ['title' => 'Admission', 'url' => route('admission')],
                ['title' => 'News & Notices', 'url' => route('news')],
                ['title' => 'Events', 'url' => route('events')],
                ['title' => 'Contact', 'url' => route('contact')],
            ],
            'contact' => [
                'address' => WebsiteSetting::get('contact_address', 'ABC Public School, New Delhi, India'),
                'phone' => WebsiteSetting::get('contact_phone', '+91 9876543210'),
                'email' => WebsiteSetting::get('contact_email', 'info@abcschool.com'),
            ],
            'social' => [
                'facebook' => WebsiteSetting::get('social_facebook', '#'),
                'instagram' => WebsiteSetting::get('social_instagram', '#'),
                'youtube' => WebsiteSetting::get('social_youtube', '#'),
                'linkedin' => WebsiteSetting::get('social_linkedin', '#'),
            ],
            'copyright' => $copyright,
        ];
    }

    private function getTestimonials(): array
    {
        $dbTestimonials = Testimonial::where('status', true)->orderBy('sort_order', 'asc')->get();

        if ($dbTestimonials->isNotEmpty()) {
            return $dbTestimonials->map(fn ($t) => [
                'name' => $t->name,
                'role' => $t->role,
                'image' => $t->image_url,
                'message' => $t->message,
                'rating' => $t->rating,
            ])->toArray();
        }

        return [
            [
                'name' => 'Amit Sharma',
                'role' => 'Parent',
                'image' => asset('assets/website/images/slider/slider-1.png'),
                'message' => 'The school provides an excellent learning environment. My child has improved academically and personally.',
                'rating' => 5,
            ],
            [
                'name' => 'Priya Verma',
                'role' => 'Student',
                'image' => asset('assets/website/images/slider/slider-2.png'),
                'message' => 'Teachers are supportive and the online exam system is very easy to use.',
                'rating' => 5,
            ],
            [
                'name' => 'Rahul Singh',
                'role' => 'Parent',
                'image' => asset('assets/website/images/slider/slider-1.png'),
                'message' => 'Best school with modern facilities, discipline, and excellent teachers.',
                'rating' => 5,
            ],
        ];
    }

    private function getContact(): array
    {
        return [
            'title' => 'Get In Touch',
            'subtitle' => 'Contact Us',
            'address' => WebsiteSetting::get('contact_address', 'ABC Public School, New Delhi, India'),
            'phone' => WebsiteSetting::get('contact_phone', '+91 9876543210'),
            'email' => WebsiteSetting::get('contact_email', 'info@abcschool.com'),
            'working_hours' => WebsiteSetting::get('contact_working_hours', 'Mon - Sat : 08:00 AM - 04:00 PM'),
            'map' => WebsiteSetting::get('contact_map_url', 'https://maps.google.com'),
        ];
    }
}
