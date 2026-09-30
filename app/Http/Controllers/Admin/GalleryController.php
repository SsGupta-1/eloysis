<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\BaseController;
use App\Http\Requests\Admin\GalleryRequest;
use App\Models\Gallery;
use App\Services\Admin\GalleryService;
use Illuminate\Http\Request;

class GalleryController extends BaseController
{
    protected GalleryService $galleryService;

    public function __construct(GalleryService $galleryService)
    {
        $this->galleryService = $galleryService;
    }

    public function index()
    {
        return view('admin.website.gallery.index');
    }

    public function list(Request $request)
    {
        $filters = [
            'search' => $request->input('search.value'),
            'category' => $request->input('category'),
            'filter_status' => $request->input('filter_status'),
        ];

        $length = max((int) $request->input('length', 10), 1);
        $start = max((int) $request->input('start', 0), 0);
        $page = (int) floor($start / $length) + 1;

        $orderColumn = $request->input('order.0.column');
        $orderDirection = $request->input('order.0.dir', 'asc');

        $galleries = $this->galleryService->getGalleries(
            $filters,
            $length,
            $page,
            $orderColumn !== null ? (int) $orderColumn : null,
            $orderDirection
        );

        return $this->datatable($galleries, (int) $request->input('draw', 1));
    }

    public function store(GalleryRequest $request)
    {
        $this->galleryService->create(
            $request->validated()
        );

        return $this->success('Gallery image added successfully.');
    }

    public function edit(Gallery $gallery)
    {
        return $this->success('Gallery item fetched successfully.', [
            'id' => $gallery->id,
            'title' => $gallery->title,
            'category' => $gallery->category,
            'sort_order' => $gallery->sort_order,
            'status' => $gallery->status,
            'image_url' => $gallery->image_url,
        ]);
    }

    public function update(GalleryRequest $request, Gallery $gallery)
    {
        $this->galleryService->update(
            $gallery->id,
            $request->validated()
        );

        return $this->success('Gallery item updated successfully.');
    }

    public function destroy(Gallery $gallery)
    {
        $this->galleryService->delete($gallery->id);

        return $this->success('Gallery item deleted successfully.');
    }

    public function changeStatus(Gallery $gallery)
    {
        $this->galleryService->changeStatus($gallery->id);

        return $this->success('Gallery item status updated successfully.');
    }
}
