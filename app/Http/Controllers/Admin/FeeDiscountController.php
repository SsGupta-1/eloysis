<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\BaseController;
use App\Http\Requests\Admin\FeeDiscountRequest;
use App\Models\FeeDiscount;
use App\Services\Admin\FeeDiscountService;
use Illuminate\Http\Request;

class FeeDiscountController extends BaseController
{
    public function __construct(
        protected FeeDiscountService $feeDiscountService
    ) {}

    public function index()
    {
        return view('admin.fees.discounts.index');
    }

    public function list(Request $request)
    {
        $filters = [
            'search' => $request->input('search.value'),
            'discount_type' => $request->input('discount_type'),
            'filter_status' => $request->input('filter_status'),
        ];

        $length = max((int) $request->input('length', 10), 1);
        $start = max((int) $request->input('start', 0), 0);
        $page = (int) floor($start / $length) + 1;

        $orderColumn = $request->input('order.0.column');
        $orderDirection = $request->input('order.0.dir', 'asc');

        $discounts = $this->feeDiscountService->getFeeDiscounts(
            $filters,
            $length,
            $page,
            $orderColumn !== null ? (int) $orderColumn : null,
            $orderDirection
        );

        return $this->datatable($discounts, (int) $request->input('draw', 1));
    }

    public function store(FeeDiscountRequest $request)
    {
        $this->feeDiscountService->create($request->validated());

        return $this->success('Fee Discount created successfully.');
    }

    public function edit(FeeDiscount $fee_discount)
    {
        return $this->success('Fee Discount fetched successfully.', [
            'id' => $fee_discount->id,
            'name' => $fee_discount->name,
            'code' => $fee_discount->code,
            'discount_type' => $fee_discount->discount_type,
            'amount' => $fee_discount->amount,
            'description' => $fee_discount->description,
            'is_active' => $fee_discount->is_active,
        ]);
    }

    public function update(FeeDiscountRequest $request, FeeDiscount $fee_discount)
    {
        $this->feeDiscountService->update(
            $fee_discount->id,
            $request->validated()
        );

        return $this->success('Fee Discount updated successfully.');
    }

    public function destroy(FeeDiscount $fee_discount)
    {
        $this->feeDiscountService->delete($fee_discount->id);

        return $this->success('Fee Discount deleted successfully.');
    }

    public function changeStatus(FeeDiscount $fee_discount)
    {
        $this->feeDiscountService->changeStatus($fee_discount->id);

        return $this->success('Fee Discount status updated successfully.');
    }
}
