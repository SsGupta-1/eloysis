<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\BaseController;
use App\Models\Announcement;
use App\Models\HomePageSection;
use App\Models\ImportantMessage;
use App\Models\QuickLink;
use App\Models\Testimonial;
use App\Services\Admin\HomePageBuilderService;
use App\Services\Website\HomePageLayoutRegistry;
use App\Services\Website\HomePageService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class HomePageBuilderController extends BaseController
{
    public function __construct(
        protected HomePageBuilderService $builderService,
        protected HomePageService $homePageService
    ) {}

    /**
     * Display the Home Page Builder Dashboard.
     */
    public function index(): View
    {
        $data = $this->builderService->getBuilderData();

        return view('admin.website.builder.index', $data);
    }

    /**
     * Update section order via Drag & Drop.
     */
    public function updateOrders(Request $request): JsonResponse
    {
        $request->validate([
            'orders' => 'required|array',
            'orders.*.id' => 'required|integer|exists:home_page_sections,id',
            'orders.*.order' => 'required|integer|min:1',
        ]);

        $this->builderService->updateSectionOrders($request->input('orders'));

        return $this->success('Homepage section ordering updated successfully.');
    }

    /**
     * Toggle section enabled/disabled status.
     */
    public function toggleStatus(Request $request, HomePageSection $section): JsonResponse
    {
        $request->validate([
            'is_enabled' => 'required|boolean',
        ]);

        $this->builderService->toggleSectionStatus($section->id, (bool) $request->input('is_enabled'));

        $statusText = $request->input('is_enabled') ? 'enabled' : 'disabled';

        return $this->success("Section '{$section->title}' has been {$statusText}.");
    }

    /**
     * Update section layout variant.
     */
    public function updateLayout(Request $request, HomePageSection $section): JsonResponse
    {
        $request->validate([
            'layout_key' => 'required|string|max:100',
        ]);

        $layoutKey = $request->input('layout_key');
        if (! HomePageLayoutRegistry::isValidLayout($section->section_type, $layoutKey)) {
            return $this->error('Invalid layout selected for this section type.');
        }

        $this->builderService->updateSectionLayout($section->id, $layoutKey);

        return $this->success('Section layout variant updated successfully.');
    }

    /**
     * Update general section properties and custom settings.
     */
    public function updateSection(Request $request, HomePageSection $section): JsonResponse
    {
        $request->validate([
            'title' => 'nullable|string|max:255',
            'subtitle' => 'nullable|string|max:255',
            'layout_key' => 'nullable|string|max:100',
            'custom_class' => 'nullable|string|max:100',
            'image' => 'nullable|image|max:5120',
            'bg_image' => 'nullable|image|max:5120',
            'settings' => 'nullable|array',
        ]);

        $data = $request->all();
        if ($request->hasFile('image')) {
            $data['image'] = $request->file('image');
        }
        if ($request->hasFile('bg_image')) {
            $data['bg_image'] = $request->file('bg_image');
        }

        $updated = $this->builderService->updateSection($section->id, $data);

        return $this->success('Section updated successfully.', $updated);
    }

    /**
     * Create or Update a Custom Content Section.
     */
    public function storeCustomSection(Request $request): JsonResponse
    {
        $request->validate([
            'id' => 'nullable|integer|exists:home_page_sections,id',
            'title' => 'required|string|max:255',
            'subtitle' => 'nullable|string|max:255',
            'layout_key' => 'nullable|string|max:100',
            'custom_class' => 'nullable|string|max:100',
            'image' => 'nullable|image|max:5120',
            'bg_image' => 'nullable|image|max:5120',
            'settings' => 'nullable|array',
            'content' => 'nullable|string',
            'button_text' => 'nullable|string|max:100',
            'button_url' => 'nullable|string|max:255',
        ]);

        $data = $request->all();
        $settings = $request->input('settings', []);
        if (! is_array($settings)) {
            $settings = [];
        }

        foreach (['content', 'button_text', 'button_url', 'bg_color', 'text_color'] as $key) {
            if ($request->has($key)) {
                $settings[$key] = $request->input($key);
            }
        }
        $data['settings'] = $settings;

        if ($request->hasFile('image')) {
            $data['image'] = $request->file('image');
        }
        if ($request->hasFile('bg_image')) {
            $data['bg_image'] = $request->file('bg_image');
        }

        if ($request->filled('id')) {
            $section = $this->builderService->updateSection((int) $request->input('id'), $data);
            $message = 'Custom section updated successfully.';
        } else {
            $section = $this->builderService->createCustomSection($data);
            $message = 'Custom section created successfully.';
        }

        return $this->success($message, $section);
    }

    /**
     * Delete a Custom Content Section.
     */
    public function destroyCustomSection(HomePageSection $section): JsonResponse
    {
        if ($section->is_system) {
            return $this->error('Core system sections cannot be deleted. You can disable them instead.');
        }

        $this->builderService->deleteCustomSection($section->id);

        return $this->success('Custom section deleted successfully.');
    }

    // =========================================================================
    // ANNOUNCEMENTS
    // =========================================================================

    public function storeAnnouncement(Request $request): JsonResponse
    {
        $data = $request->validate([
            'title' => 'required|string|max:255',
            'content' => 'nullable|string',
            'badge' => 'nullable|string|max:50',
            'link_url' => 'nullable|string|max:255',
            'link_text' => 'nullable|string|max:100',
            'start_date' => 'nullable|date',
            'end_date' => 'nullable|date|after_or_equal:start_date',
            'status' => 'nullable|boolean',
            'is_active' => 'nullable|boolean',
        ]);

        $data['status'] = $request->boolean('status', $request->boolean('is_active', true));
        $announcement = $this->builderService->saveAnnouncement($data);

        return $this->success('Announcement created successfully.', $announcement);
    }

    public function updateAnnouncement(Request $request, Announcement $announcement): JsonResponse
    {
        $data = $request->validate([
            'title' => 'required|string|max:255',
            'content' => 'nullable|string',
            'badge' => 'nullable|string|max:50',
            'link_url' => 'nullable|string|max:255',
            'link_text' => 'nullable|string|max:100',
            'start_date' => 'nullable|date',
            'end_date' => 'nullable|date|after_or_equal:start_date',
            'status' => 'nullable|boolean',
            'is_active' => 'nullable|boolean',
        ]);

        $data['status'] = $request->boolean('status', $request->boolean('is_active', true));
        $updated = $this->builderService->saveAnnouncement($data, $announcement->id);

        return $this->success('Announcement updated successfully.', $updated);
    }

    public function destroyAnnouncement(Announcement $announcement): JsonResponse
    {
        $this->builderService->deleteAnnouncement($announcement->id);

        return $this->success('Announcement deleted successfully.');
    }

    // =========================================================================
    // IMPORTANT MESSAGES
    // =========================================================================

    public function storeImportantMessage(Request $request): JsonResponse
    {
        $data = $request->validate([
            'title' => 'required|string|max:255',
            'message' => 'required|string',
            'badge' => 'nullable|string|max:100',
            'type' => 'nullable|string|in:info,warning,danger,success',
            'action_text' => 'nullable|string|max:100',
            'action_url' => 'nullable|string|max:255',
            'start_date' => 'nullable|date',
            'end_date' => 'nullable|date|after_or_equal:start_date',
            'status' => 'nullable|boolean',
            'is_active' => 'nullable|boolean',
        ]);

        $data['type'] = $data['type'] ?? 'info';
        $data['status'] = $request->boolean('status', $request->boolean('is_active', true));
        $message = $this->builderService->saveImportantMessage($data);

        return $this->success('Important message created successfully.', $message);
    }

    public function updateImportantMessage(Request $request, ImportantMessage $message): JsonResponse
    {
        $data = $request->validate([
            'title' => 'required|string|max:255',
            'message' => 'required|string',
            'type' => 'required|string|in:info,warning,danger,success',
            'action_text' => 'nullable|string|max:100',
            'action_url' => 'nullable|string|max:255',
            'start_date' => 'nullable|date',
            'end_date' => 'nullable|date|after_or_equal:start_date',
            'status' => 'nullable|boolean',
        ]);

        $data['status'] = $request->boolean('status', true);
        $updated = $this->builderService->saveImportantMessage($data, $message->id);

        return $this->success('Important message updated successfully.', $updated);
    }

    public function destroyImportantMessage(ImportantMessage $message): JsonResponse
    {
        $this->builderService->deleteImportantMessage($message->id);

        return $this->success('Important message deleted successfully.');
    }

    // =========================================================================
    // TESTIMONIALS
    // =========================================================================

    public function storeTestimonial(Request $request): JsonResponse
    {
        $data = $request->validate([
            'name' => 'required|string|max:255',
            'role' => 'required|string|max:100',
            'message' => 'required|string',
            'rating' => 'nullable|integer|min:1|max:5',
            'image' => 'nullable|image|max:3072',
            'sort_order' => 'nullable|integer',
            'status' => 'nullable|boolean',
            'is_active' => 'nullable|boolean',
        ]);

        $data['rating'] = $request->input('rating', 5);
        $data['status'] = $request->boolean('status', $request->boolean('is_active', true));
        if ($request->hasFile('image')) {
            $data['image'] = $request->file('image');
        }

        $testimonial = $this->builderService->saveTestimonial($data);

        return $this->success('Testimonial created successfully.', $testimonial);
    }

    public function updateTestimonial(Request $request, Testimonial $testimonial): JsonResponse
    {
        $data = $request->validate([
            'name' => 'required|string|max:255',
            'role' => 'required|string|max:100',
            'message' => 'required|string',
            'rating' => 'nullable|integer|min:1|max:5',
            'image' => 'nullable|image|max:3072',
            'sort_order' => 'nullable|integer',
            'status' => 'nullable|boolean',
            'is_active' => 'nullable|boolean',
        ]);

        $data['status'] = $request->boolean('status', $request->boolean('is_active', true));
        if ($request->hasFile('image')) {
            $data['image'] = $request->file('image');
        }

        $updated = $this->builderService->saveTestimonial($data, $testimonial->id);

        return $this->success('Testimonial updated successfully.', $updated);
    }

    public function destroyTestimonial(Testimonial $testimonial): JsonResponse
    {
        $this->builderService->deleteTestimonial($testimonial->id);

        return $this->success('Testimonial deleted successfully.');
    }

    // =========================================================================
    // QUICK LINKS
    // =========================================================================

    public function storeQuickLink(Request $request): JsonResponse
    {
        $data = $request->validate([
            'title' => 'required|string|max:255',
            'description' => 'nullable|string|max:255',
            'icon' => 'nullable|string|max:100',
            'url' => 'required|string|max:255',
            'color' => 'nullable|string|in:primary,success,warning,info,danger,dark',
            'sort_order' => 'nullable|integer',
            'status' => 'nullable|boolean',
            'is_active' => 'nullable|boolean',
        ]);

        $data['color'] = $data['color'] ?? 'primary';
        $data['status'] = $request->boolean('status', $request->boolean('is_active', true));
        $link = $this->builderService->saveQuickLink($data);

        return $this->success('Quick link created successfully.', $link);
    }

    public function updateQuickLink(Request $request, QuickLink $quick_link): JsonResponse
    {
        $data = $request->validate([
            'title' => 'required|string|max:255',
            'description' => 'nullable|string|max:255',
            'icon' => 'nullable|string|max:100',
            'url' => 'required|string|max:255',
            'color' => 'nullable|string|in:primary,success,warning,info,danger,dark',
            'sort_order' => 'nullable|integer',
            'status' => 'nullable|boolean',
            'is_active' => 'nullable|boolean',
        ]);

        $data['status'] = $request->boolean('status', $request->boolean('is_active', true));
        $updated = $this->builderService->saveQuickLink($data, $quick_link->id);

        return $this->success('Quick link updated successfully.', $updated);
    }

    public function destroyQuickLink(QuickLink $quick_link): JsonResponse
    {
        $this->builderService->deleteQuickLink($quick_link->id);

        return $this->success('Quick link deleted successfully.');
    }

    /**
     * Preview Homepage.
     */
    public function preview(): View
    {
        $pageData = $this->homePageService->getPageData();

        return view('website.home', compact('pageData'));
    }
}
