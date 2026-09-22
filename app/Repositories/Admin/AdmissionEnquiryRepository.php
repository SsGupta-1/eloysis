<?php

namespace App\Repositories\Admin;

use App\Models\AdmissionEnquiry;
use App\Models\StudentProfile;
use App\Models\User;
use App\Repositories\BaseRepository;
use Exception;

class AdmissionEnquiryRepository extends BaseRepository
{
    /**
     * Create a new class instance.
     */
    public function __construct(AdmissionEnquiry $model)
    {
        parent::__construct($model);
    }

    /**
     * Get admission enquiries listing for DataTable
     */
    public function get(array $filters, int $length, int $page, ?int $orderColumn, string $orderDirection): array
    {
        try {
            $query = $this->model->newQuery()->with([
                'academicSession:id,name',
                'class:id,class_name',
                'handledBy:id,name',
                'assignedUser:id,name',
                'studentProfile:id,admission_no,user_id',
                'enrollment:id,stu_profile_id,academic_session_id,class_id,section_id,roll_number',
            ]);

            // Academic session filter
            $query->when(
                ! empty($filters['academic_session_id']),
                function ($query) use ($filters) {
                    $query->where('academic_session_id', $filters['academic_session_id']);
                }
            );

            // Class filter
            $query->when(
                ! empty($filters['class_id']),
                function ($query) use ($filters) {
                    $query->where('class_id', $filters['class_id']);
                }
            );

            // Status filter
            $query->when(
                ! empty($filters['status']),
                function ($query) use ($filters) {
                    $query->where('status', $filters['status']);
                }
            );

            // Source filter
            $query->when(
                ! empty($filters['source']),
                function ($query) use ($filters) {
                    $query->where('source', $filters['source']);
                }
            );

            // Assigned To / Handled By filter
            $query->when(
                ! empty($filters['assigned_to']) || ! empty($filters['handled_by']),
                function ($query) use ($filters) {
                    $staffId = $filters['assigned_to'] ?? $filters['handled_by'];
                    $query->where(function ($q) use ($staffId) {
                        $q->where('assigned_to', $staffId)
                            ->orWhere('handled_by', $staffId);
                    });
                }
            );

            // Start date filter
            $query->when(
                ! empty($filters['start_date']),
                function ($query) use ($filters) {
                    $query->whereDate('created_at', '>=', $filters['start_date']);
                }
            );

            // End date filter
            $query->when(
                ! empty($filters['end_date']),
                function ($query) use ($filters) {
                    $query->whereDate('created_at', '<=', $filters['end_date']);
                }
            );

            // Search filter
            $query->when(
                ! empty($filters['search']),
                function ($query) use ($filters) {
                    $term = $filters['search'];
                    $query->where(function ($query) use ($term) {
                        $query->where('student_name', 'like', "%{$term}%")
                            ->orWhere('student_email', 'like', "%{$term}%")
                            ->orWhere('student_phone', 'like', "%{$term}%")
                            ->orWhere('alternate_phone', 'like', "%{$term}%")
                            ->orWhere('application_no', 'like', "%{$term}%")
                            ->orWhere('parent_name', 'like', "%{$term}%")
                            ->orWhere('parent_phone', 'like', "%{$term}%");
                    });
                }
            );

            // Ordering
            $sortableColumns = [
                1 => 'application_no',
                2 => 'student_name',
                8 => 'status',
                10 => 'attempt_count',
                11 => 'next_follow_up_at',
                12 => 'created_at',
            ];

            $orderColumnIndex = $orderColumn ?? null;
            if ($orderColumnIndex !== null && isset($sortableColumns[$orderColumnIndex])) {
                $query->orderBy($sortableColumns[$orderColumnIndex], $orderDirection === 'desc' ? 'desc' : 'asc');
            } else {
                $query->latest('id');
            }

            // Pagination
            $admissionEnquiries = $query->paginate($length, ['*'], 'page', $page);

            return [
                'draw' => (int) ($filters['draw'] ?? 1),
                'recordsTotal' => $admissionEnquiries->total(),
                'recordsFiltered' => $admissionEnquiries->total(),
                'data' => $admissionEnquiries->items(),
            ];

        } catch (Exception $exception) {
            throw $exception;
        }
    }

    /**
     * Find enquiry with complete relations
     */
    public function findWithRelations(int|string $id): ?AdmissionEnquiry
    {
        return $this->model
            ->with([
                'academicSession:id,name',
                'class:id,class_name',
                'handledBy:id,name',
                'assignedUser:id,name',
                'assignedBy:id,name',
                'convertedBy:id,name',
                'studentProfile.user',
                'enrollment.academicSession',
                'enrollment.studentClass',
                'enrollment.section',
                'followups.user:id,name',
            ])
            ->find($id);
    }

    /**
     * Check for duplicate student/user records based on phone or email
     */
    public function checkDuplicates(AdmissionEnquiry $enquiry): array
    {
        $duplicates = [];

        // Check users by email
        if (! empty($enquiry->student_email)) {
            $userByEmail = User::where('email', $enquiry->student_email)->first();
            if ($userByEmail) {
                $duplicates[] = [
                    'type' => 'user_email',
                    'field' => 'Email',
                    'value' => $enquiry->student_email,
                    'record_id' => $userByEmail->id,
                    'name' => $userByEmail->name,
                    'details' => "Existing user account with email {$enquiry->student_email} (User ID: #{$userByEmail->id})",
                ];
            }
        }

        // Check users/students by student phone
        if (! empty($enquiry->student_phone)) {
            $userByPhone = User::where('mobile', $enquiry->student_phone)->first();
            if ($userByPhone) {
                $duplicates[] = [
                    'type' => 'user_phone',
                    'field' => 'Student Phone',
                    'value' => $enquiry->student_phone,
                    'record_id' => $userByPhone->id,
                    'name' => $userByPhone->name,
                    'details' => "Existing user account with mobile {$enquiry->student_phone} (User ID: #{$userByPhone->id})",
                ];
            }
        }

        // Check guardian mobile in student profiles
        if (! empty($enquiry->parent_phone)) {
            $profileByParentPhone = StudentProfile::with('user:id,name')
                ->where('guardian_mobile', $enquiry->parent_phone)
                ->first();

            if ($profileByParentPhone) {
                $duplicates[] = [
                    'type' => 'guardian_phone',
                    'field' => 'Parent Phone',
                    'value' => $enquiry->parent_phone,
                    'record_id' => $profileByParentPhone->id,
                    'name' => $profileByParentPhone->user?->name ?? $profileByParentPhone->father_name ?? 'Student',
                    'details' => "Existing student profile with guardian mobile {$enquiry->parent_phone} (Admission No: {$profileByParentPhone->admission_no})",
                ];
            }
        }

        return $duplicates;
    }
}
