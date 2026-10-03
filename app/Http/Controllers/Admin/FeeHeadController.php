<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\BaseController;
use App\Http\Requests\Admin\FeeHeadRequest;
use App\Models\FeeHead;
use App\Services\Admin\FeeHeadService;
use Illuminate\Http\Request;

class FeeHeadController extends BaseController
{
    public function __construct(
        protected FeeHeadService $feeHeadService
    ) {}

    public function index()
    {
        return view('admin.fees.heads.index');
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

        $feeHeads = $this->feeHeadService->getFeeHeads(
            $filters,
            $length,
            $page,
            $orderColumn !== null ? (int) $orderColumn : null,
            $orderDirection
        );

        return $this->datatable($feeHeads, (int) $request->input('draw', 1));
    }

    public function store(FeeHeadRequest $request)
    {
        $this->feeHeadService->create($request->validated());

        return $this->success('Fee Head created successfully.');
    }

    public function edit(FeeHead $fee_head)
    {
        return $this->success('Fee Head fetched successfully.', [
            'id' => $fee_head->id,
            'name' => $fee_head->name,
            'code' => $fee_head->code,
            'description' => $fee_head->description,
            'is_active' => $fee_head->is_active,
        ]);
    }

    public function update(FeeHeadRequest $request, FeeHead $fee_head)
    {
        $this->feeHeadService->update(
            $fee_head->id,
            $request->validated()
        );

        return $this->success('Fee Head updated successfully.');
    }

    public function destroy(FeeHead $fee_head)
    {
        $this->feeHeadService->delete($fee_head->id);

        return $this->success('Fee Head deleted successfully.');
    }

    public function changeStatus(FeeHead $fee_head)
    {
        $this->feeHeadService->changeStatus($fee_head->id);

        return $this->success('Fee Head status updated successfully.');
    }
}
