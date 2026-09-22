<?php

namespace App\Http\Controllers\Website;

use App\Http\Controllers\BaseController;
use App\Http\Requests\Website\ContactRequest;
use App\Services\Website\ContactService;

class ContactController extends BaseController
{
    protected ContactService $contactService;

    public function __construct(ContactService $contactService)
    {
        $this->contactService = $contactService;
    }

    public function store(ContactRequest $request)
    {
        try {
            // dd($request->validated());
            $this->contactService->store(
                $request->validated()
            );

            return $this->success(
                'Thank you! Your message has been sent successfully.'
            );

        } catch (\Throwable $e) {

            return $this->error(
                'Unable to send message.'
            );

        }
    }
}
