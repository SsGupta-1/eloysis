<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\BaseController;
use Illuminate\Http\Request;
use App\Http\Requests\Admin\AdmissionEnquiryRequest;
use App\Services\Admin\AdmissionEnquiryService;
use App\Models\AdmissionEnquiry;
use App\Models\User;

class AdmissionEnquiryController extends BaseController
{
    protected $AdmissionEnquiryService;

    public function __construct(AdmissionEnquiryService $AdmissionEnquiryService)
    {
        $this->AdmissionEnquiryService = $AdmissionEnquiryService;
    }
    

    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        
         return view(
            'admin.admission_enquiries.index',
            [

                'academicSessions' =>
                    academic_session_options(),

                'classes' =>
                    class_options(),

                'statuses' =>
                    admission_enquiry_status_options(),

                'sources' => [

                    'website' => 'Website',

                    'reference' => 'Reference',

                    'walk_in' => 'Walk In',

                    'phone' => 'Phone',

                    'other' => 'Other',

                ],

                'users' =>
                    User::query()
                        ->where('status', true)
                        ->orderBy('name')
                        ->pluck('name', 'id')
                        ->toArray(),

            ]
        );
    }

    /**
     * Summary of list
     * @param \Illuminate\Http\Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function list(Request $request)
    {
        $filters = [
            'search' => $request->input('search.value'),
            'academic_session_id' => $request->input('academic_session_id'),
            'status' => $request->input('status'),
            'source' => $request->input('source'),
            'class' => $request->input('class'),
            'handled_by' => $request->input('assigned_to'),
            'start_date' => $request->input('start_date'),
            'end_date' => $request->input('end_date'),
        ];

        $length = max((int) $request->input('length', 10), 1);
        $start = max((int) $request->input('start', 0), 0);
        $page = (int) floor($start / $length) + 1;

        $orderColumn = $request->input('order.0.column');
        $orderDirection = $request->input('order.0.dir', 'asc');

        $admissionEnquiry = $this->AdmissionEnquiryService->getAdmissionEnquiries(
            $filters,
            $length,
            $page,
            $orderColumn !== null ? (int) $orderColumn : null,
            $orderDirection
        );

        return $this->datatable($admissionEnquiry, (int) $request->input('draw', 1));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        return view('admin.admission_enquiry.create');
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(AdmissionEnquiryRequest $request)
    {
        $admissionEnquiry = $this->AdmissionEnquiryService->create($request->validated());

        return $this->success(
            'Admission Enquiry created successfully.',
            $admissionEnquiry
        );
    }

    /**
     * Display the specified resource.
     */
    public function show(AdmissionEnquiry $admissionEnquiry): View
    {
        return view(
            'admin.admission_enquiries.view',
            [
                'enquiry' => $admissionEnquiry,
                'statuses' => admission_enquiry_status_options(),
                'attemptStatuses' => admission_attempt_status_options(),
                'users' => User::query()->where('status', true)->orderBy('name')->pluck('name', 'id')
                        ->toArray(),

            ]
        );
    }
   
    /**
     * Show the form for editing the specified resource.
     */
    public function edit(string $id)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
     public function update(
        AdmissionEnquiryRequest $request,
        int $id
    ): JsonResponse {

        $enquiry =
            $this->service->find($id);


        abort_if(
            ! $enquiry,
            404
        );


        $enquiry =
            $this->service->update(
                $enquiry,
                $request->validated()
            );


        return response()->json([

            'status' => true,

            'message' =>
                'Admission enquiry updated successfully.',

            'data' => [

                'id' =>
                    $enquiry->id,

                'status' =>
                    $enquiry->status,

                'attempt_count' =>
                    $enquiry->attempt_count,

            ],

        ]);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        //
    }

    /**
     * Change status
     */
    public function changeStatus(string $id)
    {
        $admissionEnquiry = $this->AdmissionEnquiryService->changeStatus($id);

        return $this->success(
            'Admission Enquiry status updated successfully.',
            $admissionEnquiry
        );
    }
}
    