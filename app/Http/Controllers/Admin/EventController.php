<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\BaseController;
use App\Http\Requests\Admin\EventRequest;
use App\Models\Event;
use App\Services\Admin\EventService;
use Illuminate\Http\Request;

class EventController extends BaseController
{
    protected EventService $eventService;

    public function __construct(EventService $eventService)
    {
        $this->eventService = $eventService;
    }

    public function index()
    {
        return view('admin.website.events.index');
    }

    public function list(Request $request)
    {
        $filters = [
            'search' => $request->input('search.value'),
            'filter_status' => $request->input('filter_status'),
        ];

        $length = max((int) $request->input('length', 10), 1);
        $start = max((int) $request->input('start', 0), 0);
        $page = (int) floor($start / $length) + 1;

        $orderColumn = $request->input('order.0.column');
        $orderDirection = $request->input('order.0.dir', 'asc');

        $events = $this->eventService->getEvents(
            $filters,
            $length,
            $page,
            $orderColumn !== null ? (int) $orderColumn : null,
            $orderDirection
        );

        return $this->datatable($events, (int) $request->input('draw', 1));
    }

    public function store(EventRequest $request)
    {
        $this->eventService->create(
            $request->validated()
        );

        return $this->success('Event created successfully.');
    }

    public function edit(Event $event)
    {
        return $this->success('Event fetched successfully.', [
            'id' => $event->id,
            'title' => $event->title,
            'event_date' => $event->event_date ? $event->event_date->format('Y-m-d') : null,
            'event_time' => $event->event_time,
            'location' => $event->location,
            'description' => $event->description,
            'url' => $event->url,
            'status' => $event->status,
            'image_url' => $event->image_url,
        ]);
    }

    public function update(EventRequest $request, Event $event)
    {
        $this->eventService->update(
            $event->id,
            $request->validated()
        );

        return $this->success('Event updated successfully.');
    }

    public function destroy(Event $event)
    {
        $this->eventService->delete($event->id);

        return $this->success('Event deleted successfully.');
    }

    public function changeStatus(Event $event)
    {
        $this->eventService->changeStatus($event->id);

        return $this->success('Event status updated successfully.');
    }
}
