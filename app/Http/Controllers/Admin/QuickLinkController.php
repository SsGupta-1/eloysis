<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\BaseController;
use App\Http\Requests\Admin\QuickLinkRequest;
use App\Models\QuickLink;
use App\Services\Admin\QuickLinkService;
use Illuminate\Http\Request;

class QuickLinkController extends BaseController
{
    public function __construct(
        protected QuickLinkService $quickLinkService
    ) {}

    public function index()
    {
        return view('admin.website.quick_links.index');
    }

    public function list(Request $request)
    {
        $filters = [
            'search' => $request->input('search.value'),
            'color' => $request->input('color'),
            'filter_status' => $request->input('filter_status'),
        ];

        $length = max((int) $request->input('length', 10), 1);
        $start = max((int) $request->input('start', 0), 0);
        $page = (int) floor($start / $length) + 1;

        $orderColumn = $request->input('order.0.column');
        $orderDirection = $request->input('order.0.dir', 'asc');

        $links = $this->quickLinkService->getQuickLinks(
            $filters,
            $length,
            $page,
            $orderColumn !== null ? (int) $orderColumn : null,
            $orderDirection
        );

        return $this->datatable($links, (int) $request->input('draw', 1));
    }

    public function store(QuickLinkRequest $request)
    {
        $this->quickLinkService->create($request->validated());

        return $this->success('Quick link created successfully.');
    }

    public function edit(QuickLink $quick_link)
    {
        return $this->success('Quick link fetched successfully.', [
            'id' => $quick_link->id,
            'title' => $quick_link->title,
            'description' => $quick_link->description,
            'icon' => $quick_link->icon,
            'url' => $quick_link->url,
            'color' => $quick_link->color,
            'sort_order' => $quick_link->sort_order,
            'status' => $quick_link->status,
        ]);
    }

    public function update(QuickLinkRequest $request, QuickLink $quick_link)
    {
        $this->quickLinkService->update(
            $quick_link->id,
            $request->validated()
        );

        return $this->success('Quick link updated successfully.');
    }

    public function destroy(QuickLink $quick_link)
    {
        $this->quickLinkService->delete($quick_link->id);

        return $this->success('Quick link deleted successfully.');
    }

    public function changeStatus(QuickLink $quick_link)
    {
        $this->quickLinkService->changeStatus($quick_link->id);

        return $this->success('Quick link status updated successfully.');
    }
}
