<?php

namespace App\Services\Admin;

use App\Helpers\UploadHelper;
use App\Models\Announcement;
use App\Models\HomePageSection;
use App\Models\ImportantMessage;
use App\Models\QuickLink;
use App\Models\Testimonial;
use App\Repositories\Admin\HomePageSectionRepository;
use App\Services\Website\HomePageLayoutRegistry;
use Exception;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

class HomePageBuilderService
{
    public function __construct(
        protected HomePageSectionRepository $repository
    ) {}

    /**
     * Get all sections prepared for the admin builder with available layout variants.
     */
    public function getBuilderData(): array
    {
        $sections = $this->repository->getAllSections();
        $registry = HomePageLayoutRegistry::getAll();

        $sectionsData = $sections->map(function ($section) use ($registry) {
            $typeMeta = $registry[$section->section_type] ?? [
                'name' => ucfirst(str_replace('_', ' ', $section->section_type)),
                'description' => '',
                'icon' => 'bi-puzzle',
                'layouts' => [],
            ];

            return [
                'id' => $section->id,
                'section_key' => $section->section_key,
                'section_type' => $section->section_type,
                'type_name' => $typeMeta['name'],
                'type_description' => $typeMeta['description'],
                'type_icon' => $typeMeta['icon'],
                'title' => $section->title,
                'subtitle' => $section->subtitle,
                'is_enabled' => (bool) $section->is_enabled,
                'display_order' => (int) $section->display_order,
                'layout_key' => $section->layout_key,
                'custom_class' => $section->custom_class,
                'settings' => $section->settings ?? [],
                'is_system' => (bool) $section->is_system,
                'available_layouts' => $typeMeta['layouts'] ?? [],
            ];
        })->toArray();

        return [
            'sections' => $sectionsData,
            'registry' => $registry,
            'announcements' => $this->repository->getAnnouncements(),
            'important_messages' => $this->repository->getImportantMessages(),
            'testimonials' => $this->repository->getTestimonials(),
            'quick_links' => $this->repository->getQuickLinks(),
        ];
    }

    /**
     * Save drag-and-drop section order.
     */
    public function updateSectionOrders(array $orders): bool
    {
        DB::beginTransaction();

        try {
            $this->repository->updateOrders($orders);
            DB::commit();

            return true;
        } catch (Exception $e) {
            DB::rollBack();
            throw $e;
        }
    }

    /**
     * Toggle section visibility.
     */
    public function toggleSectionStatus(int $id, bool $isEnabled): bool
    {
        return $this->repository->toggleStatus($id, $isEnabled);
    }

    /**
     * Update section layout.
     */
    public function updateSectionLayout(int $id, string $layoutKey): bool
    {
        $section = $this->repository->find($id);
        if (! $section) {
            throw new Exception('Section not found.');
        }

        $validLayout = HomePageLayoutRegistry::resolveLayout($section->section_type, $layoutKey);

        return $section->update(['layout_key' => $validLayout]);
    }

    /**
     * Update section details and custom settings.
     */
    public function updateSection(int $id, array $data): HomePageSection
    {
        $section = $this->repository->find($id);
        if (! $section) {
            throw new Exception('Section not found.');
        }

        DB::beginTransaction();

        try {
            $updateData = [
                'title' => $data['title'] ?? $section->title,
                'subtitle' => $data['subtitle'] ?? $section->subtitle,
                'custom_class' => $data['custom_class'] ?? null,
            ];

            if (isset($data['layout_key'])) {
                $updateData['layout_key'] = HomePageLayoutRegistry::resolveLayout(
                    $section->section_type,
                    $data['layout_key']
                );
            }

            // Handle custom settings / uploads
            $settings = $section->settings ?? [];
            if (! is_array($settings)) {
                $settings = [];
            }

            if (isset($data['settings']) && is_array($data['settings'])) {
                $settings = array_merge($settings, $data['settings']);
            }
            foreach (['content', 'button_text', 'button_url', 'bg_color', 'text_color'] as $key) {
                if (isset($data[$key])) {
                    $settings[$key] = $data[$key];
                }
            }

            // Handle settings image upload if present
            if (isset($data['image']) && $data['image'] instanceof UploadedFile) {
                $oldImage = $settings['image'] ?? null;
                $settings['image'] = UploadHelper::replace($data['image'], $oldImage, 'assets/uploads/website/custom');
            }

            if (isset($data['bg_image']) && $data['bg_image'] instanceof UploadedFile) {
                $oldBg = $settings['bg_image'] ?? null;
                $settings['bg_image'] = UploadHelper::replace($data['bg_image'], $oldBg, 'assets/uploads/website/custom');
            }

            $updateData['settings'] = $settings;

            $section->update($updateData);

            DB::commit();

            return $section;
        } catch (Exception $e) {
            DB::rollBack();
            throw $e;
        }
    }

    /**
     * Create a new Custom Content Section.
     */
    public function createCustomSection(array $data): HomePageSection
    {
        DB::beginTransaction();

        try {
            $maxOrder = (int) HomePageSection::max('display_order');
            $uniqueSlug = 'custom_'.Str::slug($data['title'] ?? 'section').'_'.Str::random(4);

            $settings = $data['settings'] ?? [];
            if (! is_array($settings)) {
                $settings = [];
            }
            foreach (['content', 'button_text', 'button_url', 'bg_color', 'text_color'] as $key) {
                if (isset($data[$key]) && ! isset($settings[$key])) {
                    $settings[$key] = $data[$key];
                }
            }

            if (isset($data['image']) && $data['image'] instanceof UploadedFile) {
                $settings['image'] = UploadHelper::upload($data['image'], 'assets/uploads/website/custom');
            }

            if (isset($data['bg_image']) && $data['bg_image'] instanceof UploadedFile) {
                $settings['bg_image'] = UploadHelper::upload($data['bg_image'], 'assets/uploads/website/custom');
            }

            $layoutKey = HomePageLayoutRegistry::resolveLayout('custom_content', $data['layout_key'] ?? 'custom_content_01');

            $section = HomePageSection::create([
                'section_key' => $uniqueSlug,
                'section_type' => 'custom_content',
                'title' => $data['title'],
                'subtitle' => $data['subtitle'] ?? null,
                'is_enabled' => true,
                'display_order' => $maxOrder + 1,
                'layout_key' => $layoutKey,
                'custom_class' => $data['custom_class'] ?? null,
                'settings' => $settings,
                'is_system' => false,
            ]);

            DB::commit();

            return $section;
        } catch (Exception $e) {
            DB::rollBack();
            throw $e;
        }
    }

    /**
     * Delete a custom section.
     */
    public function deleteCustomSection(int $id): bool
    {
        return $this->repository->delete($id);
    }

    // ==========================================
    // ANNOUNCEMENTS CRUD
    // ==========================================

    public function saveAnnouncement(array $data, ?int $id = null): Announcement
    {
        if ($id) {
            $announcement = Announcement::findOrFail($id);
            $announcement->update($data);

            return $announcement;
        }

        return Announcement::create($data);
    }

    public function deleteAnnouncement(int $id): bool
    {
        $announcement = Announcement::findOrFail($id);

        return $announcement->delete();
    }

    // ==========================================
    // IMPORTANT MESSAGES CRUD
    // ==========================================

    public function saveImportantMessage(array $data, ?int $id = null): ImportantMessage
    {
        if ($id) {
            $message = ImportantMessage::findOrFail($id);
            $message->update($data);

            return $message;
        }

        return ImportantMessage::create($data);
    }

    public function deleteImportantMessage(int $id): bool
    {
        $message = ImportantMessage::findOrFail($id);

        return $message->delete();
    }

    // ==========================================
    // TESTIMONIALS CRUD
    // ==========================================

    public function saveTestimonial(array $data, ?int $id = null): Testimonial
    {
        if (isset($data['image']) && $data['image'] instanceof UploadedFile) {
            $existing = $id ? Testimonial::find($id) : null;
            $data['image'] = UploadHelper::replace(
                $data['image'],
                $existing?->image,
                'assets/uploads/website/testimonials'
            );
        }

        if ($id) {
            $testimonial = Testimonial::findOrFail($id);
            $testimonial->update($data);

            return $testimonial;
        }

        return Testimonial::create($data);
    }

    public function deleteTestimonial(int $id): bool
    {
        $testimonial = Testimonial::findOrFail($id);
        if ($testimonial->image) {
            UploadHelper::delete($testimonial->image);
        }

        return $testimonial->delete();
    }

    // ==========================================
    // QUICK LINKS CRUD
    // ==========================================

    public function saveQuickLink(array $data, ?int $id = null): QuickLink
    {
        if ($id) {
            $link = QuickLink::findOrFail($id);
            $link->update($data);

            return $link;
        }

        return QuickLink::create($data);
    }

    public function deleteQuickLink(int $id): bool
    {
        $link = QuickLink::findOrFail($id);

        return $link->delete();
    }
}
