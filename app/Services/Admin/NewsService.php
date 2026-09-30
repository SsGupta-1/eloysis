<?php

namespace App\Services\Admin;

use App\Helpers\UploadHelper;
use App\Repositories\Admin\NewsRepository;
use Exception;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

class NewsService
{
    protected NewsRepository $newsRepository;

    public function __construct(NewsRepository $newsRepository)
    {
        $this->newsRepository = $newsRepository;
    }

    public function getNews(
        array $filters = [],
        int $perPage = 10,
        int $page = 1,
        ?int $orderColumn = null,
        string $orderDirection = 'asc'
    ) {
        return $this->newsRepository->getNews($filters, $perPage, $page, $orderColumn, $orderDirection);
    }

    public function create(array $data)
    {
        DB::beginTransaction();

        try {
            $data['slug'] = Str::slug($data['title']).'-'.Str::random(5);

            if (isset($data['image']) && $data['image'] instanceof UploadedFile) {
                $data['image'] = UploadHelper::upload($data['image'], 'assets/uploads/website/news');
            }

            $news = $this->newsRepository->create($data);

            DB::commit();

            return $news;
        } catch (Exception $e) {
            DB::rollBack();
            throw $e;
        }
    }

    public function update(int $id, array $data)
    {
        DB::beginTransaction();

        try {
            $news = $this->newsRepository->find($id);

            if (isset($data['image']) && $data['image'] instanceof UploadedFile) {
                $data['image'] = UploadHelper::replace($data['image'], $news->image, 'assets/uploads/website/news');
            } else {
                unset($data['image']);
            }

            $news = $this->newsRepository->update($id, $data);

            DB::commit();

            return $news;
        } catch (Exception $e) {
            DB::rollBack();
            throw $e;
        }
    }

    public function delete(int $id)
    {
        DB::beginTransaction();

        try {
            $news = $this->newsRepository->find($id);
            if ($news && $news->image) {
                UploadHelper::delete($news->image);
            }

            $this->newsRepository->delete($id);

            DB::commit();

            return true;
        } catch (Exception $e) {
            DB::rollBack();
            throw $e;
        }
    }

    public function changeStatus(int $id)
    {
        return $this->newsRepository->changeStatus($id);
    }
}
