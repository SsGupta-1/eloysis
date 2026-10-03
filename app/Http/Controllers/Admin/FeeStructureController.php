<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\BaseController;
use App\Http\Requests\Admin\FeeStructureRequest;
use App\Models\AcademicClass;
use App\Models\AcademicSession;
use App\Models\FeeGroup;
use App\Models\FeeHead;
use App\Models\FeeStructure;
use App\Services\Admin\FeeStructureService;
use Illuminate\Http\Request;

class FeeStructureController extends BaseController
{
    public function __construct(
        protected FeeStructureService $feeStructureService
    ) {}

    public function index()
    {
        $academicSessions = AcademicSession::orderByDesc('id')->pluck('name', 'id')->toArray();
        $academicClasses = AcademicClass::orderBy('class_name')->pluck('class_name', 'id')->toArray();
        $feeHeads = FeeHead::where('is_active', true)->orderBy('name')->pluck('name', 'id')->toArray();
        $feeGroups = FeeGroup::where('is_active', true)->orderBy('name')->pluck('name', 'id')->toArray();

        return view('admin.fees.structures.index', compact('academicSessions', 'academicClasses', 'feeHeads', 'feeGroups'));
    }

    public function list(Request $request)
    {
        $filters = [
            'search' => $request->input('search.value'),
            'academic_session_id' => $request->input('academic_session_id'),
            'academic_class_id' => $request->input('academic_class_id'),
            'fee_head_id' => $request->input('fee_head_id'),
            'frequency' => $request->input('frequency'),
            'filter_status' => $request->input('filter_status'),
        ];

        $length = max((int) $request->input('length', 10), 1);
        $start = max((int) $request->input('start', 0), 0);
        $page = (int) floor($start / $length) + 1;

        $orderColumn = $request->input('order.0.column');
        $orderDirection = $request->input('order.0.dir', 'asc');

        $structures = $this->feeStructureService->getFeeStructures(
            $filters,
            $length,
            $page,
            $orderColumn !== null ? (int) $orderColumn : null,
            $orderDirection
        );

        return $this->datatable($structures, (int) $request->input('draw', 1));
    }

    public function store(FeeStructureRequest $request)
    {
        $this->feeStructureService->create($request->validated());

        return $this->success('Fee Structure created successfully.');
    }

    public function edit(FeeStructure $fee_structure)
    {
        return $this->success('Fee Structure fetched successfully.', [
            'id' => $fee_structure->id,
            'academic_session_id' => $fee_structure->academic_session_id,
            'academic_class_id' => $fee_structure->academic_class_id,
            'fee_head_id' => $fee_structure->fee_head_id,
            'fee_group_id' => $fee_structure->fee_group_id,
            'amount' => $fee_structure->amount,
            'frequency' => $fee_structure->frequency,
            'due_date' => $fee_structure->due_date?->format('Y-m-d'),
            'fine_type' => $fee_structure->fine_type,
            'fine_amount' => $fee_structure->fine_amount,
            'is_active' => $fee_structure->is_active,
        ]);
    }

    public function update(FeeStructureRequest $request, FeeStructure $fee_structure)
    {
        $this->feeStructureService->update(
            $fee_structure->id,
            $request->validated()
        );

        return $this->success('Fee Structure updated successfully.');
    }

    public function destroy(FeeStructure $fee_structure)
    {
        $this->feeStructureService->delete($fee_structure->id);

        return $this->success('Fee Structure deleted successfully.');
    }

    public function changeStatus(FeeStructure $fee_structure)
    {
        $this->feeStructureService->changeStatus($fee_structure->id);

        return $this->success('Fee Structure status updated successfully.');
    }
}
