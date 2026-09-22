<?php

namespace App\Services\Admin;

use App\Models\AdmissionEnquiry;
use App\Models\AdmissionEnquiryFollowup;
use App\Models\StudentEnrollment;
use App\Repositories\Admin\AdmissionEnquiryRepository;
use Exception;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class AdmissionEnquiryService
{
    public function __construct(
        protected AdmissionEnquiryRepository $admissionEnquiryRepository,
        protected StudentService $studentService
    ) {}

    /**
     * Get admission enquiries listing
     */
    public function getAdmissionEnquiries(
        array $filters,
        int $length,
        int $page,
        ?int $orderColumn,
        string $orderDirection
    ): array {
        return $this->admissionEnquiryRepository->get($filters, $length, $page, $orderColumn, $orderDirection);
    }

    /**
     * Find enquiry by ID with relations
     */
    public function find(int|string $id): ?AdmissionEnquiry
    {
        return $this->admissionEnquiryRepository->findWithRelations($id);
    }

    /**
     * Create enquiry
     */
    public function create(array $data): AdmissionEnquiry
    {
        return DB::transaction(function () use ($data) {
            // Auto generate application_no if missing
            if (empty($data['application_no'])) {
                $datePrefix = now()->format('dmY');
                $lastEnquiry = AdmissionEnquiry::query()
                    ->where('application_no', 'like', $datePrefix.'%')
                    ->orderByDesc('id')
                    ->lockForUpdate()
                    ->first();

                $sequence = $lastEnquiry ? ((int) substr($lastEnquiry->application_no, -4)) + 1 : 1;
                $data['application_no'] = $datePrefix.str_pad($sequence, 4, '0', STR_PAD_LEFT);
            }

            // Track assignment if specified on creation
            if (! empty($data['assigned_to'])) {
                $data['assigned_at'] = now();
                $data['assigned_by'] = Auth::guard('admin')->id();
                $data['handled_by'] = $data['assigned_to'];
                if (empty($data['status']) || $data['status'] === 'new') {
                    $data['status'] = 'assigned';
                }
            } else {
                $data['status'] = $data['status'] ?? 'new';
            }

            $enquiry = $this->admissionEnquiryRepository->create($data);

            // Log creation activity
            AdmissionEnquiryFollowup::create([
                'admission_enquiry_id' => $enquiry->id,
                'user_id' => Auth::guard('admin')->id(),
                'action_type' => 'created',
                'status' => $enquiry->status ?? 'new',
                'remarks' => $enquiry->remarks ?? 'Admission enquiry recorded in admin panel.',
                'next_follow_up_at' => $enquiry->next_follow_up_at ?? null,
            ]);

            return $enquiry;
        });
    }

    /**
     * Update enquiry
     */
    public function update(AdmissionEnquiry $enquiry, array $data): AdmissionEnquiry
    {
        return DB::transaction(function () use ($enquiry, $data) {
            $oldStatus = $enquiry->status;
            $oldAssignedTo = $enquiry->assigned_to;

            $updateData = [
                'status' => $data['status'] ?? $enquiry->status,
                'assigned_to' => array_key_exists('assigned_to', $data) ? $data['assigned_to'] : $enquiry->assigned_to,
                'handled_by' => array_key_exists('assigned_to', $data) ? $data['assigned_to'] : $enquiry->handled_by,
                'next_follow_up_at' => $data['next_follow_up_at'] ?? $data['next_followup_at'] ?? $enquiry->next_follow_up_at,
            ];

            // If staff assignment changed
            if (! empty($updateData['assigned_to']) && $updateData['assigned_to'] != $oldAssignedTo) {
                $updateData['assigned_at'] = now();
                $updateData['assigned_by'] = Auth::guard('admin')->id();

                AdmissionEnquiryFollowup::create([
                    'admission_enquiry_id' => $enquiry->id,
                    'user_id' => Auth::guard('admin')->id(),
                    'action_type' => 'assignment',
                    'status' => $updateData['status'],
                    'remarks' => 'Assigned enquiry to staff member (User ID: #'.$updateData['assigned_to'].').',
                ]);
            }

            // If call attempt / remark is logged
            if (! empty($data['attempt_status']) || ! empty($data['attempt_remarks']) || ! empty($data['remarks'])) {
                $updateData['attempt_count'] = ((int) $enquiry->attempt_count) + 1;
                $updateData['last_attempt_at'] = now();
                $updateData['last_attempt_status'] = $data['attempt_status'] ?? null;
                $updateData['last_contacted_at'] = now();

                AdmissionEnquiryFollowup::create([
                    'admission_enquiry_id' => $enquiry->id,
                    'user_id' => Auth::guard('admin')->id(),
                    'action_type' => 'follow_up',
                    'status' => $updateData['status'],
                    'attempt_status' => $data['attempt_status'] ?? null,
                    'remarks' => $data['attempt_remarks'] ?? $data['remarks'] ?? null,
                    'next_follow_up_at' => $updateData['next_follow_up_at'] ?? null,
                ]);
            } elseif ($oldStatus !== $updateData['status']) {
                // Status changed without call attempt
                AdmissionEnquiryFollowup::create([
                    'admission_enquiry_id' => $enquiry->id,
                    'user_id' => Auth::guard('admin')->id(),
                    'action_type' => 'status_change',
                    'status' => $updateData['status'],
                    'remarks' => 'Status changed from '.ucfirst(str_replace('_', ' ', $oldStatus)).' to '.ucfirst(str_replace('_', ' ', $updateData['status'])).'.',
                    'next_follow_up_at' => $updateData['next_follow_up_at'] ?? null,
                ]);
            }

            $enquiry->update($updateData);

            return $enquiry->fresh(['assignedUser', 'handledBy', 'followups.user']);
        });
    }

    /**
     * Assign staff to enquiry
     */
    public function assignStaff(AdmissionEnquiry $enquiry, ?int $staffId, ?string $remarks = null): AdmissionEnquiry
    {
        return DB::transaction(function () use ($enquiry, $staffId, $remarks) {
            $oldStaff = $enquiry->assignedUser?->name ?? 'Unassigned';

            $enquiry->update([
                'assigned_to' => $staffId,
                'handled_by' => $staffId,
                'assigned_at' => now(),
                'assigned_by' => Auth::guard('admin')->id(),
                'status' => ($enquiry->status === 'new' && $staffId) ? 'assigned' : $enquiry->status,
            ]);

            $enquiry->load('assignedUser');
            $newStaff = $enquiry->assignedUser?->name ?? 'Unassigned';

            AdmissionEnquiryFollowup::create([
                'admission_enquiry_id' => $enquiry->id,
                'user_id' => Auth::guard('admin')->id(),
                'action_type' => 'assignment',
                'status' => $enquiry->status,
                'remarks' => $remarks ?? "Staff assignment updated from [{$oldStaff}] to [{$newStaff}].",
            ]);

            return $enquiry;
        });
    }

    /**
     * Add follow-up remark and attempt
     */
    public function addFollowup(AdmissionEnquiry $enquiry, array $data): AdmissionEnquiryFollowup
    {
        return DB::transaction(function () use ($enquiry, $data) {
            $updateData = [
                'attempt_count' => ((int) $enquiry->attempt_count) + 1,
                'last_attempt_at' => now(),
                'last_contacted_at' => now(),
            ];

            if (! empty($data['attempt_status'])) {
                $updateData['last_attempt_status'] = $data['attempt_status'];
            }

            if (! empty($data['status'])) {
                $updateData['status'] = $data['status'];
            }

            if (! empty($data['next_follow_up_at'])) {
                $updateData['next_follow_up_at'] = $data['next_follow_up_at'];
            }

            $enquiry->update($updateData);

            return AdmissionEnquiryFollowup::create([
                'admission_enquiry_id' => $enquiry->id,
                'user_id' => Auth::guard('admin')->id(),
                'action_type' => 'follow_up',
                'status' => $enquiry->status,
                'attempt_status' => $data['attempt_status'] ?? null,
                'remarks' => $data['remarks'] ?? null,
                'next_follow_up_at' => $data['next_follow_up_at'] ?? null,
            ]);
        });
    }

    /**
     * Check duplicate student/user records
     */
    public function checkDuplicates(AdmissionEnquiry $enquiry): array
    {
        return $this->admissionEnquiryRepository->checkDuplicates($enquiry);
    }

    /**
     * Convert Enquiry to Admission using existing Student creation workflow
     *
     * @throws Exception
     */
    public function convertToAdmission(AdmissionEnquiry $enquiry, array $studentData)
    {
        if ($enquiry->isConverted()) {
            throw new Exception('This enquiry has already been converted to an admission.');
        }

        return DB::transaction(function () use ($enquiry, $studentData) {
            // 1. Delegate to the existing StudentService to create User, StudentProfile & Enrollment
            $studentProfile = $this->studentService->create($studentData);

            // Fetch the newly created enrollment
            $enrollment = StudentEnrollment::where('stu_profile_id', $studentProfile->id)
                ->latest('id')
                ->first();

            // 2. Link enquiry with the created StudentProfile, User & Enrollment
            $enquiry->update([
                'status' => 'converted',
                'converted_at' => now(),
                'converted_by' => Auth::guard('admin')->id(),
                'student_profile_id' => $studentProfile->id,
                'enrollment_id' => $enrollment?->id,
                'converted_user_id' => $studentProfile->user_id,
            ]);

            // 3. Log conversion in follow-up activity history
            AdmissionEnquiryFollowup::create([
                'admission_enquiry_id' => $enquiry->id,
                'user_id' => Auth::guard('admin')->id(),
                'action_type' => 'converted',
                'status' => 'converted',
                'remarks' => "Enquiry successfully converted to Student Admission. Admission No: {$studentProfile->admission_no}".($enrollment ? ", Roll No: {$enrollment->roll_number}" : ''),
            ]);

            return [
                'student' => $studentProfile,
                'enrollment' => $enrollment,
                'enquiry' => $enquiry->fresh(['studentProfile', 'enrollment', 'convertedUser']),
            ];
        });
    }
}
