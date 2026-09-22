<?php

namespace App\Services\Website;

use App\Models\AdmissionEnquiry;
use Illuminate\Support\Facades\DB;

class AdmissionService
{
    /**
     * Save Admission Message
     */
    public function store(array $data): AdmissionEnquiry
    {
        return DB::transaction(function () use ($data) {

            /*
            |--------------------------------------------------------------------------
            | Enquiry Number
            |--------------------------------------------------------------------------
            |
            | Format:
            | DDMMYYYY + 4 digit sequence
            |
            | Example:
            | 280820260001
            |
            */

            $datePrefix = now()->format('dmY');

            $lastEnquiry = AdmissionEnquiry::query()
                ->where('application_no', 'like', $datePrefix.'%')
                ->orderByDesc('id')
                ->lockForUpdate()
                ->first();

            if ($lastEnquiry) {

                $lastSequence = (int) substr(
                    $lastEnquiry->application_no,
                    -4
                );

                $sequence = $lastSequence + 1;

            } else {

                $sequence = 1;

            }

            /*
            |--------------------------------------------------------------------------
            | Four Digit Sequence
            |--------------------------------------------------------------------------
            */

            $sequenceNumber = str_pad(
                $sequence,
                4,
                '0',
                STR_PAD_LEFT
            );

            /*
            |--------------------------------------------------------------------------
            | Final Enquiry Number
            |--------------------------------------------------------------------------
            */

            $enquiryNo =
                $datePrefix.
                $sequenceNumber;

            /*
            |--------------------------------------------------------------------------
            | Create Enquiry
            |--------------------------------------------------------------------------
            */

            return AdmissionEnquiry::create([

                'application_no' => $enquiryNo,

                'academic_session_id' => array_key_first(academic_session_options(1)) ?? null,

                'student_name' => $data['student_name'],
                'student_email' => $data['student_email'],
                'student_phone' => $data['student_phone'],
                'alternate_phone' => $data['alternate_phone'] ?? null,
                'date_of_birth' => $data['date_of_birth'] ?? null,

                'gender' => $data['gender'] ?? null,

                'parent_name' => $data['parent_name'],

                'parent_phone' => $data['parent_phone'],

                'class_id' => $data['class_id'] ?? null,

                'source' => $data['source'] ?? 'website',

                'reference_type' => $data['reference_type'] ?? null,

                'reference_id' => $data['reference_id'] ?? null,

                'reference_name' => $data['reference_name'] ?? null,

                'reference_phone' => $data['reference_phone'] ?? null,

                'message' => $data['message'] ?? null,

                'status' => 'new',

                'ip_address' => request()->ip(),

                'user_agent' => request()->userAgent(),

            ]);
        });
    }
}
