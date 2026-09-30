<?php

namespace App\Services\Admin;

use App\Helpers\UploadHelper;
use App\Repositories\Admin\GalleryRepository;
use Exception;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\DB;

class GalleryService
{
    protected GalleryRepository $galleryRepository;

    public function __construct(GalleryRepository $galleryRepository)
    {
        $this->galleryRepository = $galleryRepository;
    }

    public function getGalleries(
        array $filters = [],
        int $perPage = 10,
        int $page = 1,
        ?int $orderColumn = null,
        string $orderDirection = 'asc'
    ) {
        return $this->galleryRepository->getGalleries($filters, $perPage, $page, $orderColumn, $orderDirection);
    }

    public function create(array $data)
    {
        DB::beginTransaction();

        try {
            if (isset($data['image']) && $data['image'] instanceof UploadedFile) {
                $data['image'] = UploadHelper::upload($data['image'], 'assets/uploads/website/gallery');
            }

            $gallery = $this->galleryRepository->create($data);

            DB::commit();

            return $gallery;
        } catch (Exception $e) {
            DB::rollBack();
            throw $e;
        }
    }

    public function update(int $id, array $data)
    {
        DB::beginTransaction();

        try {
            $gallery = $this->galleryRepository->find($id);

            if (isset($data['image']) && $data['image'] instanceof UploadedFile) {
                $data['image'] = UploadHelper::replace($data['image'], $gallery->image, 'assets/uploads/website/gallery');
            } else {
                unset($data['image']);
            }

            $gallery = $this->galleryRepository->update($id, $data);

            DB::commit();

            return $gallery;
        } catch (Exception $e) {
            DB::rollBack();
            throw $e;
        }
    }

    public function delete(int $id)
    {
        DB::beginTransaction();

        try {
            $gallery = $this->galleryRepository->find($id);
            if ($gallery && $gallery->image) {
                UploadHelper::delete($gallery->image);
            }

            $this->galleryRepository->delete($id);

            DB::commit();

            return true;
        } catch (Exception $e) {
            DB::rollBack();
            throw $e;
        }
    }

    public function changeStatus(int $id)
    {
        return $this->galleryRepository->changeStatus($id);
    }
}
