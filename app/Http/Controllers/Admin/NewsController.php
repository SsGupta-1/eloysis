<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\BaseController;
use App\Http\Requests\Admin\NewsRequest;
use App\Models\News;
use App\Services\Admin\NewsService;
use Illuminate\Http\Request;

class NewsController extends BaseController
{
    protected NewsService $newsService;

    public function __construct(NewsService $newsService)
    {
        $this->newsService = $newsService;
    }

    public function index()
    {
        return view('admin.website.news.index');
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

        $news = $this->newsService->getNews(
            $filters,
            $length,
            $page,
            $orderColumn !== null ? (int) $orderColumn : null,
            $orderDirection
        );

        return $this->datatable($news, (int) $request->input('draw', 1));
    }

    public function store(NewsRequest $request)
    {
        $this->newsService->create(
            $request->validated()
        );

        return $this->success('News item created successfully.');
    }

    public function edit(News $news)
    {
        return $this->success('News fetched successfully.', [
            'id' => $news->id,
            'title' => $news->title,
            'published_date' => $news->published_date ? $news->published_date->format('Y-m-d') : null,
            'summary' => $news->summary,
            'content' => $news->content,
            'url' => $news->url,
            'status' => $news->status,
            'image_url' => $news->image_url,
        ]);
    }

    public function update(NewsRequest $request, News $news)
    {
        $this->newsService->update(
            $news->id,
            $request->validated()
        );

        return $this->success('News item updated successfully.');
    }

    public function destroy(News $news)
    {
        $this->newsService->delete($news->id);

        return $this->success('News item deleted successfully.');
    }

    public function changeStatus(News $news)
    {
        $this->newsService->changeStatus($news->id);

        return $this->success('News status updated successfully.');
    }
}
