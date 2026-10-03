<?php

namespace App\Repositories\Admin;

use App\Models\FeeDiscount;
use App\Models\FeeStructure;
use App\Models\StudentEnrollment;
use App\Models\StudentFeeAllocation;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Support\Facades\DB;

class FeeAllocationRepository
{
    public function getAllocations(
        array $filters = [],
        int $perPage = 10,
        int $page = 1,
        ?int $orderColumn = null,
        string $orderDirection = 'asc'
    ): LengthAwarePaginator {
        $query = StudentFeeAllocation::with([
            'enrollment.studentClass',
            'enrollment.section',
            'student.user',
            'academicSession',
            'feeHead',
            'feeDiscount',
        ]);

        if (! empty($filters['search'])) {
            $search = $filters['search'];
            $query->where(function ($q) use ($search) {
                $q->where('title', 'like', "%{$search}%")
                    ->orWhereHas('student.user', function ($uq) use ($search) {
                        $uq->where('name', 'like', "%{$search}%")
                            ->orWhere('email', 'like', "%{$search}%");
                    })
                    ->orWhereHas('student', function ($sq) use ($search) {
                        $sq->where('admission_no', 'like', "%{$search}%")
                            ->orWhere('father_name', 'like', "%{$search}%");
                    })
                    ->orWhereHas('enrollment', function ($eq) use ($search) {
                        $eq->where('roll_number', 'like', "%{$search}%");
                    });
            });
        }

        if (! empty($filters['academic_session_id'])) {
            $query->where('academic_session_id', $filters['academic_session_id']);
        }

        if (! empty($filters['class_id'])) {
            $query->whereHas('enrollment', function ($q) use ($filters) {
                $q->where('class_id', $filters['class_id']);
            });
        }

        if (! empty($filters['section_id'])) {
            $query->whereHas('enrollment', function ($q) use ($filters) {
                $q->where('section_id', $filters['section_id']);
            });
        }

        if (! empty($filters['fee_head_id'])) {
            $query->where('fee_head_id', $filters['fee_head_id']);
        }

        if (! empty($filters['status'])) {
            $query->where('status', $filters['status']);
        }

        if (! empty($filters['month'])) {
            $query->where('month', $filters['month']);
        }

        $columns = [
            0 => 'id',
            1 => 'stu_profile_id',
            2 => 'academic_session_id',
            3 => 'fee_head_id',
            4 => 'amount',
            5 => 'discount_amount',
            6 => 'paid_amount',
            7 => 'due_date',
            8 => 'status',
        ];

        if ($orderColumn !== null && isset($columns[$orderColumn])) {
            $query->orderBy($columns[$orderColumn], $orderDirection);
        } else {
            $query->latest('id');
        }

        return $query->paginate($perPage, ['*'], 'page', $page);
    }

    public function create(array $data): StudentFeeAllocation
    {
        $enrollment = StudentEnrollment::with('student')->findOrFail($data['student_enrollment_id']);
        $data['stu_profile_id'] = $enrollment->stu_profile_id;
        $data['status'] = 'unpaid';
        $data['paid_amount'] = 0;

        return StudentFeeAllocation::create($data);
    }

    /**
     * Allocate fees in bulk to all students of a class / section.
     */
    public function bulkAllocate(array $data): int
    {
        return DB::transaction(function () use ($data) {
            $sessionId = $data['academic_session_id'];
            $classId = $data['academic_class_id'];
            $sectionId = $data['section_id'] ?? null;
            $structureIds = $data['fee_structure_ids'] ?? [];
            $month = $data['month'] ?? null;
            $year = $data['year'] ?? date('Y');
            $customDueDate = $data['due_date'] ?? null;
            $discountId = $data['fee_discount_id'] ?? null;

            $discount = $discountId ? FeeDiscount::find($discountId) : null;

            $studentsQuery = StudentEnrollment::with(['feeDiscount', 'student'])
                ->where('academic_session_id', $sessionId)
                ->where('class_id', $classId);

            if (! empty($sectionId)) {
                $studentsQuery->where('section_id', $sectionId);
            }

            $enrollments = $studentsQuery->get();
            $structures = FeeStructure::with('feeHead')->whereIn('id', $structureIds)->get();

            $count = 0;

            foreach ($enrollments as $enrollment) {
                // Determine which discount to apply:
                // If an override discountId is passed, use that; otherwise use student's assigned standing discount
                $activeDiscount = null;
                if (! empty($discountId) && $discountId !== 'student_assigned' && $discountId !== 'none') {
                    $activeDiscount = FeeDiscount::find($discountId);
                } elseif ($discountId !== 'none') {
                    $activeDiscount = $enrollment->feeDiscount;
                }

                foreach ($structures as $structure) {
                    $amount = (float) $structure->amount;
                    $discountAmount = 0;

                    if ($activeDiscount && $activeDiscount->is_active) {
                        if ($activeDiscount->discount_type === 'percentage') {
                            $discountAmount = ($amount * (float) $activeDiscount->amount) / 100;
                        } else {
                            $discountAmount = min($amount, (float) $activeDiscount->amount);
                        }
                    }

                    $dueDate = $customDueDate ?? $structure->due_date?->format('Y-m-d') ?? now()->addDays(15)->format('Y-m-d');
                    $title = $structure->feeHead?->name.($month ? " - {$month} {$year}" : '');

                    // Check if already allocated for the exact same student, session, structure and month
                    $exists = StudentFeeAllocation::where('student_enrollment_id', $enrollment->id)
                        ->where('academic_session_id', $sessionId)
                        ->where('fee_structure_id', $structure->id)
                        ->when($month, fn ($q) => $q->where('month', $month)->where('year', $year))
                        ->exists();

                    if (! $exists) {
                        StudentFeeAllocation::create([
                            'student_enrollment_id' => $enrollment->id,
                            'stu_profile_id' => $enrollment->stu_profile_id,
                            'academic_session_id' => $sessionId,
                            'fee_structure_id' => $structure->id,
                            'fee_head_id' => $structure->fee_head_id,
                            'fee_discount_id' => $activeDiscount?->id,
                            'title' => $title,
                            'month' => $month,
                            'year' => $year,
                            'due_date' => $dueDate,
                            'amount' => $amount,
                            'discount_amount' => $discountAmount,
                            'fine_amount' => 0,
                            'paid_amount' => 0,
                            'status' => 'unpaid',
                        ]);
                        $count++;
                    }
                }
            }

            return $count;
        });
    }

    public function update(int $id, array $data): StudentFeeAllocation
    {
        $allocation = StudentFeeAllocation::findOrFail($id);

        if ($allocation->paid_amount > 0) {
            // Only allow editing due_date and title if already partially/fully paid
            $allocation->update([
                'title' => $data['title'] ?? $allocation->title,
                'due_date' => $data['due_date'] ?? $allocation->due_date,
            ]);
        } else {
            $allocation->update($data);
        }

        return $allocation;
    }

    public function delete(int $id): bool
    {
        $allocation = StudentFeeAllocation::findOrFail($id);
        if ($allocation->paid_amount > 0) {
            throw new \Exception('Cannot delete a fee allocation that has payments.');
        }

        return $allocation->delete();
    }
}
