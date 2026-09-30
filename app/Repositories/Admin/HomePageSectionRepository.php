<?php

namespace App\Repositories\Admin;

use App\Models\Announcement;
use App\Models\HomePageSection;
use App\Models\ImportantMessage;
use App\Models\QuickLink;
use App\Models\Testimonial;

class HomePageSectionRepository
{
    /**
     * Get all sections ordered by display_order.
     */
    public function getAllSections()
    {
        return HomePageSection::orderBy('display_order', 'asc')->get();
    }

    /**
     * Find section by ID.
     */
    public function find(int $id): ?HomePageSection
    {
        return HomePageSection::find($id);
    }

    /**
     * Find section by unique section_key.
     */
    public function findByKey(string $key): ?HomePageSection
    {
        return HomePageSection::where('section_key', $key)->first();
    }

    /**
     * Create a new section.
     */
    public function create(array $data): HomePageSection
    {
        return HomePageSection::create($data);
    }

    /**
     * Update section by ID.
     */
    public function update(int $id, array $data): bool
    {
        $section = $this->find($id);

        return $section ? $section->update($data) : false;
    }

    /**
     * Delete custom section by ID.
     */
    public function delete(int $id): bool
    {
        $section = $this->find($id);
        if ($section && ! $section->is_system) {
            return $section->delete();
        }

        return false;
    }

    /**
     * Update display order for multiple sections.
     * $orders is an array of ['id' => 1, 'order' => 1], ...
     */
    public function updateOrders(array $orders): void
    {
        foreach ($orders as $item) {
            HomePageSection::where('id', $item['id'])->update([
                'display_order' => $item['order'],
            ]);
        }
    }

    /**
     * Toggle section status.
     */
    public function toggleStatus(int $id, bool $isEnabled): bool
    {
        $section = $this->find($id);

        return $section ? $section->update(['is_enabled' => $isEnabled]) : false;
    }

    /**
     * Get all announcements.
     */
    public function getAnnouncements()
    {
        return Announcement::orderBy('sort_order', 'asc')->orderByDesc('id')->get();
    }

    /**
     * Get all important messages.
     */
    public function getImportantMessages()
    {
        return ImportantMessage::orderBy('sort_order', 'asc')->orderByDesc('id')->get();
    }

    /**
     * Get all testimonials.
     */
    public function getTestimonials()
    {
        return Testimonial::orderBy('sort_order', 'asc')->orderByDesc('id')->get();
    }

    /**
     * Get all quick links.
     */
    public function getQuickLinks()
    {
        return QuickLink::orderBy('sort_order', 'asc')->orderBy('id', 'asc')->get();
    }
}
