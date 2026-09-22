<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\BaseController;
use App\Http\Requests\Admin\AdmissionEnquiryRequest;
use App\Http\Requests\Admin\StudentRequest;
use App\Models\User;
use App\Services\Admin\AdmissionEnquiryService;
use App\Services\Admin\StudentService;
use Exception;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class AdmissionEnquiryController extends BaseController
{
    public function __construct(
        protected AdmissionEnquiryService $admissionEnquiryService,
        protected StudentService $studentService
    ) {}

    /**
     * Display a listing of the resource.
     */
    public function index(): View
    {
        return view(
            'admin.admission_enquiries.index',
            [
                'academicSessions' => academic_session_options(),
                'classes' => class_options(),
                'statuses' => admission_enquiry_status_options(),
                'sources' => [
                    'website' => 'Website',
                    'walk_in' => 'Walk In',
                    'reference' => 'Reference',
                    'google' => 'Google',
                    'facebook' => 'Facebook',
                    'instagram' => 'Instagram',
                    'advertisement' => 'Advertisement',
                    'phone' => 'Phone',
                    'other' => 'Other',
                ],
                'users' => User::query()
                    ->where('status', true)
                    ->orderBy('name')
                    ->pluck('name', 'id')
                    ->toArray(),
            ]
        );
    }

    /**
     * DataTables AJAX list
     */
    public function list(Request $request): JsonResponse
    {
        $filters = [
            'search' => $request->input('search.value'),
            'academic_session_id' => $request->input('academic_session_id'),
            'status' => $request->input('status'),
            'source' => $request->input('source'),
            'class_id' => $request->input('class_id'),
            'assigned_to' => $request->input('assigned_to'),
            'start_date' => $request->input('start_date'),
            'end_date' => $request->input('end_date'),
            'draw' => (int) $request->input('draw', 1),
        ];

        $length = max((int) $request->input('length', 10), 1);
        $start = max((int) $request->input('start', 0), 0);
        $page = (int) floor($start / $length) + 1;

        $orderColumn = $request->input('order.0.column');
        $orderDirection = $request->input('order.0.dir', 'asc');

        $admissionEnquiry = $this->admissionEnquiryService->getAdmissionEnquiries(
            $filters,
            $length,
            $page,
            $orderColumn !== null ? (int) $orderColumn : null,
            $orderDirection
        );

        return $this->datatable($admissionEnquiry, (int) $request->input('draw', 1));
    }

    /**
     * Show the form for creating a new enquiry manually in admin panel.
     */
    public function create(): View
    {
        return view(
            'admin.admission_enquiries.create',
            [
                'academicSessions' => academic_session_options(),
                'classes' => class_options(),
                'statuses' => admission_enquiry_status_options(),
                'sources' => [
                    'walk_in' => 'Walk In',
                    'phone' => 'Phone Call',
                    'website' => 'Website',
                    'reference' => 'Reference',
                    'google' => 'Google Search',
                    'facebook' => 'Facebook',
                    'instagram' => 'Instagram',
                    'advertisement' => 'Advertisement',
                    'other' => 'Other',
                ],
                'users' => User::query()
                    ->where('status', true)
                    ->orderBy('name')
                    ->pluck('name', 'id')
                    ->toArray(),
            ]
        );
    }

    /**
     * Store a newly created enquiry.
     */
    public function store(AdmissionEnquiryRequest $request): JsonResponse
    {
        $admissionEnquiry = $this->admissionEnquiryService->create($request->validated());

        return response()->json([
            'status' => true,
            'message' => 'Admission Enquiry created successfully.',
            'redirect_url' => route('admin.admission-enquiry.show', $admissionEnquiry->id),
            'data' => $admissionEnquiry,
        ]);
    }

    /**
     * Display the specified enquiry detail.
     */
    public function show(int|string $id): View
    {
        $enquiry = $this->admissionEnquiryService->find($id);

        abort_if(! $enquiry, 404, 'Admission Enquiry not found.');

        $duplicates = $this->admissionEnquiryService->checkDuplicates($enquiry);

        return view(
            'admin.admission_enquiries.view',
            [
                'enquiry' => $enquiry,
                'statuses' => admission_enquiry_status_options(),
                'attemptStatuses' => admission_attempt_status_options(),
                'users' => User::query()
                    ->where('status', true)
                    ->orderBy('name')
                    ->pluck('name', 'id')
                    ->toArray(),
                'duplicates' => $duplicates,
            ]
        );
    }

    /**
     * Update enquiry CRM details
     */
    public function update(Request $request, int|string $id): JsonResponse
    {
        $enquiry = $this->admissionEnquiryService->find($id);

        abort_if(! $enquiry, 404, 'Admission Enquiry not found.');

        $validated = $request->validate([
            'status' => 'required|string',
            'assigned_to' => 'nullable|exists:users,id',
            'attempt_status' => 'nullable|string',
            'attempt_remarks' => 'nullable|string|max:1000',
            'next_followup_at' => 'nullable|date',
        ]);

        $enquiry = $this->admissionEnquiryService->update($enquiry, $validated);

        return response()->json([
            'status' => true,
            'message' => 'Admission enquiry updated successfully.',
            'data' => [
                'id' => $enquiry->id,
                'status' => $enquiry->status,
                'attempt_count' => $enquiry->attempt_count,
            ],
        ]);
    }

    /**
     * Assign / Reassign Staff
     */
    public function assignStaff(Request $request, int|string $id): JsonResponse
    {
        $enquiry = $this->admissionEnquiryService->find($id);

        abort_if(! $enquiry, 404, 'Admission Enquiry not found.');

        $validated = $request->validate([
            'assigned_to' => 'nullable|exists:users,id',
            'remarks' => 'nullable|string|max:500',
        ]);

        $enquiry = $this->admissionEnquiryService->assignStaff(
            $enquiry,
            $validated['assigned_to'] ? (int) $validated['assigned_to'] : null,
            $validated['remarks'] ?? null
        );

        return $this->success(
            'Staff assigned successfully.',
            $enquiry
        );
    }

    /**
     * Add Followup Remark
     */
    public function addFollowup(Request $request, int|string $id): JsonResponse
    {
        $enquiry = $this->admissionEnquiryService->find($id);

        abort_if(! $enquiry, 404, 'Admission Enquiry not found.');

        $validated = $request->validate([
            'attempt_status' => 'nullable|string',
            'status' => 'nullable|string',
            'remarks' => 'required|string|max:1000',
            'next_follow_up_at' => 'nullable|date',
        ]);

        $followup = $this->admissionEnquiryService->addFollowup($enquiry, $validated);

        return $this->success(
            'Follow-up recorded successfully.',
            $followup
        );
    }

    /**
     * Check duplicate student/user records
     */
    public function checkDuplicates(int|string $id): JsonResponse
    {
        $enquiry = $this->admissionEnquiryService->find($id);

        abort_if(! $enquiry, 404, 'Admission Enquiry not found.');

        $duplicates = $this->admissionEnquiryService->checkDuplicates($enquiry);

        return response()->json([
            'status' => true,
            'has_duplicates' => count($duplicates) > 0,
            'duplicates' => $duplicates,
        ]);
    }

    /**
     * Display Convert to Admission Form
     */
    public function convert(int|string $id): View|RedirectResponse
    {
        $enquiry = $this->admissionEnquiryService->find($id);

        abort_if(! $enquiry, 404, 'Admission Enquiry not found.');

        if ($enquiry->isConverted()) {
            return redirect()
                ->route('admin.admission-enquiry.show', $enquiry->id)
                ->with('error', 'This enquiry has already been converted to an admission.');
        }

        $duplicates = $this->admissionEnquiryService->checkDuplicates($enquiry);

        // Auto-generate suggested admission number if not provided
        $suggestedAdmissionNo = 'ADM-'.date('Y').'-'.str_pad($enquiry->id, 4, '0', STR_PAD_LEFT);

        $sections = section_options();
        $firstSectionId = ! empty($sections) ? array_key_first($sections) : null;
        $suggestedRollNo = $this->studentService->generateSuggestedRollNumber(
            $enquiry->academic_session_id,
            $enquiry->class_id,
            $firstSectionId
        );

        return view('admin.admission_enquiries.convert', [
            'enquiry' => $enquiry,
            'academicSessions' => academic_session_options(),
            'classes' => class_options(),
            'sections' => $sections,
            'duplicates' => $duplicates,
            'suggestedAdmissionNo' => $suggestedAdmissionNo,
            'suggestedRollNo' => $suggestedRollNo,
        ]);
    }

    /**
     * Process Convert to Admission
     */
    public function storeConversion(StudentRequest $request, int|string $id): JsonResponse
    {
        $enquiry = $this->admissionEnquiryService->find($id);

        abort_if(! $enquiry, 404, 'Admission Enquiry not found.');

        if ($enquiry->isConverted()) {
            return response()->json([
                'status' => false,
                'message' => 'This enquiry has already been converted to an admission.',
            ], 422);
        }

        try {
            $result = $this->admissionEnquiryService->convertToAdmission(
                $enquiry,
                $request->validated()
            );

            return response()->json([
                'status' => true,
                'message' => 'Enquiry successfully converted to Student Admission!',
                'redirect_url' => route('admin.students.show', $result['enrollment']?->id ?? $result['student']->id),
                'data' => $result,
            ]);
        } catch (Exception $e) {
            return response()->json([
                'status' => false,
                'message' => 'Conversion failed: '.$e->getMessage(),
            ], 500);
        }
    }

    /**
     * Change status toggle
     */
    public function changeStatus(int|string $id): JsonResponse
    {
        $enquiry = $this->admissionEnquiryService->find($id);

        abort_if(! $enquiry, 404, 'Admission Enquiry not found.');

        $newStatus = $enquiry->status === 'cancelled' ? 'new' : 'cancelled';
        $enquiry = $this->admissionEnquiryService->update($enquiry, ['status' => $newStatus]);

        return $this->success(
            'Admission Enquiry status updated successfully.',
            $enquiry
        );
    }
}
