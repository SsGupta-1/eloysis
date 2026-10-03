<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\BaseController;
use App\Http\Requests\Admin\FeeAllocationRequest;
use App\Http\Requests\Admin\FeeBulkAllocationRequest;
use App\Models\AcademicClass;
use App\Models\AcademicSession;
use App\Models\FeeDiscount;
use App\Models\FeeHead;
use App\Models\FeeStructure;
use App\Models\Section;
use App\Models\StudentEnrollment;
use App\Models\StudentFeeAllocation;
use App\Services\Admin\FeeAllocationService;
use Illuminate\Http\Request;

class FeeAllocationController extends BaseController
{
    public function __construct(
        protected FeeAllocationService $feeAllocationService
    ) {}

    public function index()
    {
        $academicSessions = AcademicSession::orderByDesc('id')->pluck('name', 'id')->toArray();
        $academicClasses = AcademicClass::orderBy('class_name')->pluck('class_name', 'id')->toArray();
        $sections = Section::orderBy('name')->pluck('name', 'id')->toArray();
        $feeHeads = FeeHead::where('is_active', true)->orderBy('name')->pluck('name', 'id')->toArray();
        $feeDiscounts = FeeDiscount::where('is_active', true)->orderBy('name')->pluck('name', 'id')->toArray();
        $feeDiscountsList = FeeDiscount::where('is_active', true)->get(['id', 'name', 'discount_type', 'amount']);

        return view('admin.fees.allocations.index', compact(
            'academicSessions',
            'academicClasses',
            'sections',
            'feeHeads',
            'feeDiscounts',
            'feeDiscountsList'
        ));
    }

    public function create()
    {
        return $this->index();
    }

    public function list(Request $request)
    {
        $filters = [
            'search' => $request->input('search.value'),
            'academic_session_id' => $request->input('academic_session_id'),
            'class_id' => $request->input('class_id'),
            'section_id' => $request->input('section_id'),
            'fee_head_id' => $request->input('fee_head_id'),
            'status' => $request->input('status'),
            'month' => $request->input('month'),
        ];

        $length = max((int) $request->input('length', 10), 1);
        $start = max((int) $request->input('start', 0), 0);
        $page = (int) floor($start / $length) + 1;

        $orderColumn = $request->input('order.0.column');
        $orderDirection = $request->input('order.0.dir', 'asc');

        $allocations = $this->feeAllocationService->getAllocations(
            $filters,
            $length,
            $page,
            $orderColumn !== null ? (int) $orderColumn : null,
            $orderDirection
        );

        return $this->datatable($allocations, (int) $request->input('draw', 1));
    }

    public function store(FeeAllocationRequest $request)
    {
        $this->feeAllocationService->create($request->validated());

        return $this->success('Fee allocated successfully to student.');
    }

    public function bulk(FeeBulkAllocationRequest $request)
    {
        $count = $this->feeAllocationService->bulkAllocate($request->validated());

        return $this->success("Fees successfully allocated to {$count} student record(s).");
    }

    public function edit(StudentFeeAllocation $fee_allocation)
    {
        $fee_allocation->load(['student.user', 'enrollment.studentClass', 'enrollment.section', 'feeHead', 'feeDiscount']);

        return $this->success('Fee allocation details fetched successfully.', [
            'id' => $fee_allocation->id,
            'student_enrollment_id' => $fee_allocation->student_enrollment_id,
            'student_name' => $fee_allocation->student?->user?->name ?? 'N/A',
            'admission_no' => $fee_allocation->student?->admission_no ?? 'N/A',
            'class_name' => $fee_allocation->enrollment?->studentClass?->class_name ?? 'N/A',
            'academic_session_id' => $fee_allocation->academic_session_id,
            'fee_head_id' => $fee_allocation->fee_head_id,
            'fee_structure_id' => $fee_allocation->fee_structure_id,
            'fee_discount_id' => $fee_allocation->fee_discount_id,
            'title' => $fee_allocation->title,
            'month' => $fee_allocation->month,
            'year' => $fee_allocation->year,
            'due_date' => $fee_allocation->due_date?->format('Y-m-d'),
            'amount' => $fee_allocation->amount,
            'discount_amount' => $fee_allocation->discount_amount,
            'fine_amount' => $fee_allocation->fine_amount,
            'paid_amount' => $fee_allocation->paid_amount,
            'status' => $fee_allocation->status,
        ]);
    }

    public function update(FeeAllocationRequest $request, StudentFeeAllocation $fee_allocation)
    {
        $this->feeAllocationService->update(
            $fee_allocation->id,
            $request->validated()
        );

        return $this->success('Fee allocation updated successfully.');
    }

    public function destroy(StudentFeeAllocation $fee_allocation)
    {
        try {
            $this->feeAllocationService->delete($fee_allocation->id);

            return $this->success('Fee allocation deleted successfully.');
        } catch (\Exception $e) {
            return $this->error($e->getMessage(), 422);
        }
    }

    /**
     * AJAX helper to load fee structures by session & class.
     */
    public function getStructures(Request $request)
    {
        $sessionId = $request->input('academic_session_id');
        $classId = $request->input('academic_class_id');

        $structures = FeeStructure::with('feeHead')
            ->where('is_active', true)
            ->when($sessionId, fn ($q) => $q->where('academic_session_id', $sessionId))
            ->when($classId, fn ($q) => $q->where('academic_class_id', $classId))
            ->get()
            ->map(function ($s) {
                return [
                    'id' => $s->id,
                    'name' => ($s->feeHead?->name ?? 'Fee').' (₹'.number_format($s->amount, 2).' - '.ucfirst($s->frequency).')',
                    'amount' => $s->amount,
                    'frequency' => $s->frequency,
                    'due_date' => $s->due_date?->format('Y-m-d'),
                ];
            });

        return response()->json(['status' => 'success', 'data' => $structures]);
    }

    /**
     * AJAX helper to search active students for single allocation.
     */
    public function searchStudents(Request $request)
    {
        $term = $request->input('q');
        $sessionId = $request->input('academic_session_id');
        $classId = $request->input('class_id');

        $enrollments = StudentEnrollment::with(['student.user', 'studentClass', 'section', 'feeDiscount'])
            ->when($sessionId, fn ($q) => $q->where('academic_session_id', $sessionId))
            ->when($classId, fn ($q) => $q->where('class_id', $classId))
            ->where(function ($q) use ($term) {
                if (! empty($term)) {
                    $q->where('roll_number', 'like', "%{$term}%")
                        ->orWhereHas('student.user', function ($uq) use ($term) {
                            $uq->where('name', 'like', "%{$term}%");
                        })
                        ->orWhereHas('student', function ($sq) use ($term) {
                            $sq->where('admission_no', 'like', "%{$term}%")
                                ->orWhere('father_name', 'like', "%{$term}%");
                        });
                }
            })
            ->limit(20)
            ->get()
            ->map(function ($e) {
                $discText = $e->feeDiscount ? ' [Concession: '.$e->feeDiscount->name.']' : '';

                return [
                    'id' => $e->id,
                    'discount_id' => $e->fee_discount_id,
                    'discount_type' => $e->feeDiscount?->discount_type,
                    'discount_value' => $e->feeDiscount?->amount,
                    'text' => ($e->student?->user?->name ?? 'N/A').
                        ' (Adm: '.($e->student?->admission_no ?? 'N/A').
                        ', Roll: '.($e->roll_number ?? 'N/A').
                        ', '.($e->studentClass?->class_name ?? '').'-'.($e->section?->section_name ?? '').')'.$discText,
                ];
            });

        return response()->json(['results' => $enrollments]);
    }
}
