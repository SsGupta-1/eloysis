<?php

namespace App\Http\Controllers\Website;

use App\Http\Controllers\BaseController;
use App\Http\Requests\Website\AdmissionRequest;
use App\Services\Website\AdmissionService;

class AdmissionController extends BaseController
{
    protected AdmissionService $admissionService;

    public function __construct(AdmissionService $admissionService)
    {
        $this->admissionService = $admissionService;
    }

    public function store(AdmissionRequest $request)
    {
        try {

            $enquiry = $this->admissionService->store(
                $request->validated()
            );

            return $this->success(
                'Thank you! Your admission enquiry has been submitted successfully.',
                [
                    'enquiry_no' => $enquiry->application_no,
                ]
            );

        } catch (\Throwable $e) {

            \Log::error('Error in admission enquiry: '.$e->getMessage());

            return $this->error(
                'Unable to send message.'
            );

        }
    }
}
