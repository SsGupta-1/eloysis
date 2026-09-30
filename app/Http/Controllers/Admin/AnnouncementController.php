<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\BaseController;
use App\Http\Requests\Admin\AnnouncementRequest;
use App\Models\Announcement;
use App\Services\Admin\AnnouncementService;
use Illuminate\Http\Request;

class AnnouncementController extends BaseController
{
    public function __construct(
        protected AnnouncementService $announcementService
    ) {}

    public function index()
    {
        return view('admin.website.announcements.index');
    }

    public function list(Request $request)
    {
        $filters = [
            'search' => $request->input('search.value'),
            'badge' => $request->input('badge'),
            'filter_status' => $request->input('filter_status'),
        ];

        $length = max((int) $request->input('length', 10), 1);
        $start = max((int) $request->input('start', 0), 0);
        $page = (int) floor($start / $length) + 1;

        $orderColumn = $request->input('order.0.column');
        $orderDirection = $request->input('order.0.dir', 'asc');

        $announcements = $this->announcementService->getAnnouncements(
            $filters,
            $length,
            $page,
            $orderColumn !== null ? (int) $orderColumn : null,
            $orderDirection
        );

        return $this->datatable($announcements, (int) $request->input('draw', 1));
    }

    public function store(AnnouncementRequest $request)
    {
        $this->announcementService->create($request->validated());

        return $this->success('Announcement created successfully.');
    }

    public function edit(Announcement $announcement)
    {
        return $this->success('Announcement fetched successfully.', [
            'id' => $announcement->id,
            'title' => $announcement->title,
            'content' => $announcement->content,
            'badge' => $announcement->badge,
            'link_url' => $announcement->link_url,
            'link_text' => $announcement->link_text,
            'start_date' => $announcement->start_date ? $announcement->start_date->format('Y-m-d\TH:i') : null,
            'end_date' => $announcement->end_date ? $announcement->end_date->format('Y-m-d\TH:i') : null,
            'sort_order' => $announcement->sort_order,
            'status' => $announcement->status,
        ]);
    }

    public function update(AnnouncementRequest $request, Announcement $announcement)
    {
        $this->announcementService->update(
            $announcement->id,
            $request->validated()
        );

        return $this->success('Announcement updated successfully.');
    }

    public function destroy(Announcement $announcement)
    {
        $this->announcementService->delete($announcement->id);

        return $this->success('Announcement deleted successfully.');
    }

    public function changeStatus(Announcement $announcement)
    {
        $this->announcementService->changeStatus($announcement->id);

        return $this->success('Announcement status updated successfully.');
    }
}
