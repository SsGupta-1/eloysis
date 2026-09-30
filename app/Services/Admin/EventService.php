<?php

namespace App\Services\Admin;

use App\Helpers\UploadHelper;
use App\Repositories\Admin\EventRepository;
use Exception;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

class EventService
{
    protected EventRepository $eventRepository;

    public function __construct(EventRepository $eventRepository)
    {
        $this->eventRepository = $eventRepository;
    }

    public function getEvents(
        array $filters = [],
        int $perPage = 10,
        int $page = 1,
        ?int $orderColumn = null,
        string $orderDirection = 'asc'
    ) {
        return $this->eventRepository->getEvents($filters, $perPage, $page, $orderColumn, $orderDirection);
    }

    public function create(array $data)
    {
        DB::beginTransaction();

        try {
            $data['slug'] = Str::slug($data['title']).'-'.Str::random(5);

            if (isset($data['image']) && $data['image'] instanceof UploadedFile) {
                $data['image'] = UploadHelper::upload($data['image'], 'assets/uploads/website/events');
            }

            $event = $this->eventRepository->create($data);

            DB::commit();

            return $event;
        } catch (Exception $e) {
            DB::rollBack();
            throw $e;
        }
    }

    public function update(int $id, array $data)
    {
        DB::beginTransaction();

        try {
            $event = $this->eventRepository->find($id);

            if (isset($data['image']) && $data['image'] instanceof UploadedFile) {
                $data['image'] = UploadHelper::replace($data['image'], $event->image, 'assets/uploads/website/events');
            } else {
                unset($data['image']);
            }

            $event = $this->eventRepository->update($id, $data);

            DB::commit();

            return $event;
        } catch (Exception $e) {
            DB::rollBack();
            throw $e;
        }
    }

    public function delete(int $id)
    {
        DB::beginTransaction();

        try {
            $event = $this->eventRepository->find($id);
            if ($event && $event->image) {
                UploadHelper::delete($event->image);
            }

            $this->eventRepository->delete($id);

            DB::commit();

            return true;
        } catch (Exception $e) {
            DB::rollBack();
            throw $e;
        }
    }

    public function changeStatus(int $id)
    {
        return $this->eventRepository->changeStatus($id);
    }
}
