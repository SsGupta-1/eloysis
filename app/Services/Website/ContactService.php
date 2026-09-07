<?php

namespace App\Services\Website;

use App\Models\ContactMessage;

class ContactService
{
    /**
     * Save Contact Message
     */
    public function store(array $data): ContactMessage
    {
        return ContactMessage::create([

            'name' => $data['name'],

            'email' => $data['email'],

            'phone' => $data['phone'] ?? null,

            'subject' => $data['subject'],

            'message' => $data['message'],
            'status' => 'pending',
            'ip_address' => request()->ip(),
            'user_agent' => request()->userAgent(),

        ]);
    }
}
