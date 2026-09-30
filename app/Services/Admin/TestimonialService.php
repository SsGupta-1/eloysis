<?php

namespace App\Services\Admin;

use App\Helpers\UploadHelper;
use App\Repositories\Admin\TestimonialRepository;
use Exception;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\DB;

class TestimonialService
{
    public function __construct(
        protected TestimonialRepository $testimonialRepository
    ) {}

    public function getTestimonials(
        array $filters = [],
        int $perPage = 10,
        int $page = 1,
        ?int $orderColumn = null,
        string $orderDirection = 'asc'
    ) {
        return $this->testimonialRepository->getTestimonials($filters, $perPage, $page, $orderColumn, $orderDirection);
    }

    public function create(array $data)
    {
        DB::beginTransaction();

        try {
            if (isset($data['image']) && $data['image'] instanceof UploadedFile) {
                $data['image'] = UploadHelper::upload($data['image'], 'assets/uploads/website/testimonials');
            }

            if (! isset($data['status'])) {
                $data['status'] = true;
            }

            $testimonial = $this->testimonialRepository->create($data);

            DB::commit();

            return $testimonial;
        } catch (Exception $e) {
            DB::rollBack();
            throw $e;
        }
    }

    public function update(int $id, array $data)
    {
        DB::beginTransaction();

        try {
            $testimonial = $this->testimonialRepository->find($id);

            if (isset($data['image']) && $data['image'] instanceof UploadedFile) {
                $data['image'] = UploadHelper::replace($data['image'], $testimonial->image, 'assets/uploads/website/testimonials');
            } else {
                unset($data['image']);
            }

            $testimonial = $this->testimonialRepository->update($id, $data);

            DB::commit();

            return $testimonial;
        } catch (Exception $e) {
            DB::rollBack();
            throw $e;
        }
    }

    public function delete(int $id)
    {
        DB::beginTransaction();

        try {
            $testimonial = $this->testimonialRepository->find($id);
            if ($testimonial && $testimonial->image) {
                UploadHelper::delete($testimonial->image);
            }

            $this->testimonialRepository->delete($id);

            DB::commit();

            return true;
        } catch (Exception $e) {
            DB::rollBack();
            throw $e;
        }
    }

    public function changeStatus(int $id)
    {
        return $this->testimonialRepository->changeStatus($id);
    }
}
