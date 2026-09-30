<?php

namespace App\Services\Admin;

use App\Helpers\UploadHelper;
use App\Repositories\Admin\HomeSliderRepository;
use Exception;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\DB;

class HomeSliderService
{
    protected HomeSliderRepository $sliderRepository;

    public function __construct(HomeSliderRepository $sliderRepository)
    {
        $this->sliderRepository = $sliderRepository;
    }

    public function getSliders(
        array $filters = [],
        int $perPage = 10,
        int $page = 1,
        ?int $orderColumn = null,
        string $orderDirection = 'asc'
    ) {
        return $this->sliderRepository->getSliders($filters, $perPage, $page, $orderColumn, $orderDirection);
    }

    public function create(array $data)
    {
        DB::beginTransaction();

        try {
            if (isset($data['image']) && $data['image'] instanceof UploadedFile) {
                $data['image'] = UploadHelper::upload($data['image'], 'assets/uploads/website/sliders');
            }

            $slider = $this->sliderRepository->create($data);

            DB::commit();

            return $slider;
        } catch (Exception $e) {
            DB::rollBack();
            throw $e;
        }
    }

    public function update(int $id, array $data)
    {
        DB::beginTransaction();

        try {
            $slider = $this->sliderRepository->find($id);

            if (isset($data['image']) && $data['image'] instanceof UploadedFile) {
                $data['image'] = UploadHelper::replace($data['image'], $slider->image, 'assets/uploads/website/sliders');
            } else {
                unset($data['image']);
            }

            $slider = $this->sliderRepository->update($id, $data);

            DB::commit();

            return $slider;
        } catch (Exception $e) {
            DB::rollBack();
            throw $e;
        }
    }

    public function delete(int $id)
    {
        DB::beginTransaction();

        try {
            $slider = $this->sliderRepository->find($id);
            if ($slider && $slider->image) {
                UploadHelper::delete($slider->image);
            }

            $this->sliderRepository->delete($id);

            DB::commit();

            return true;
        } catch (Exception $e) {
            DB::rollBack();
            throw $e;
        }
    }

    public function changeStatus(int $id)
    {
        return $this->sliderRepository->changeStatus($id);
    }
}
