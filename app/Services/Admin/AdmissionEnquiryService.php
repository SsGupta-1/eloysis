<?php

namespace App\Services\Admin;

use App\Repositories\Admin\AdmissionEnquiryRepository;


class AdmissionEnquiryService
{
    protected AdmissionEnquiryRepository $admissionEnquiryRepository;
    
    /**
     * Create a new class instance.
     */
    public function __construct(AdmissionEnquiryRepository $admissionEnquiryRepository)
    {
        $this->admissionEnquiryRepository = $admissionEnquiryRepository;
    }

    /**
     * Summary of getAdmissionEnquiries
     * @param array $filters
     * @param int $length
     * @param int $page
     * @param int|null $orderColumn
     * @param string $orderDirection
     * @return \Illuminate\Database\Eloquent\Collection
     */
    public function getAdmissionEnquiries(array $filters, int $length, int $page, ?int $orderColumn, string $orderDirection)
    {
        return $this->admissionEnquiryRepository->get($filters, $length, $page, $orderColumn, $orderDirection);
    }

    public function create(array $data)
    {
        return $this->admissionEnquiryRepository->create($data);
    }

     public function update(
        AdmissionEnquiry $enquiry,
        array $data
    ): AdmissionEnquiry {

        return DB::transaction(function () use (
            $enquiry,
            $data
        ) {

            $updateData = [

                'status' =>
                    $data['status'],

                'assigned_to' =>
                    $data['assigned_to'] ?? null,

                'next_followup_at' =>
                    $data['next_followup_at'] ?? null,

            ];


            /*
            |--------------------------------------------------------------------------
            | Assignment Time
            |--------------------------------------------------------------------------
            */

            if (
                ! empty($data['assigned_to']) &&
                $data['assigned_to'] != $enquiry->assigned_to
            ) {

                $updateData['assigned_at'] =
                    now();
            }


            /*
            |--------------------------------------------------------------------------
            | Attempt
            |--------------------------------------------------------------------------
            */

            if (! empty($data['attempt_status'])) {

                $updateData['attempt_count'] =
                    ((int) $enquiry->attempt_count) + 1;

                $updateData['last_attempt_at'] =
                    now();

                $updateData['last_attempt_status'] =
                    $data['attempt_status'];
            }


            /*
            |--------------------------------------------------------------------------
            | Update
            |--------------------------------------------------------------------------
            */

            return $this->repository->update(
                $enquiry,
                $updateData
            );
        });
    }


}
