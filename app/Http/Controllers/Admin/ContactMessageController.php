<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\BaseController;
use App\Models\ContactMessage;
use App\Services\Admin\ContactMessageService;
use Illuminate\Http\Request;

class ContactMessageController extends BaseController
{
    protected ContactMessageService $messageService;

    public function __construct(ContactMessageService $messageService)
    {
        $this->messageService = $messageService;
    }

    public function index()
    {
        return view('admin.website.contact_messages.index');
    }

    public function list(Request $request)
    {
        $filters = [
            'search' => $request->input('search.value'),
            'status' => $request->input('status'),
        ];

        $length = max((int) $request->input('length', 10), 1);
        $start = max((int) $request->input('start', 0), 0);
        $page = (int) floor($start / $length) + 1;

        $orderColumn = $request->input('order.0.column');
        $orderDirection = $request->input('order.0.dir', 'desc');

        $messages = $this->messageService->getMessages(
            $filters,
            $length,
            $page,
            $orderColumn !== null ? (int) $orderColumn : null,
            $orderDirection
        );

        return $this->datatable($messages, (int) $request->input('draw', 1));
    }

    public function show(ContactMessage $contactMessage)
    {
        $message = $this->messageService->find($contactMessage->id);

        return $this->success('Message fetched successfully.', $message);
    }

    public function updateStatus(Request $request, ContactMessage $contactMessage)
    {
        $request->validate([
            'status' => 'required|in:pending,read,replied',
        ]);

        $this->messageService->updateStatus($contactMessage->id, $request->status);

        return $this->success('Status updated successfully.');
    }

    public function destroy(ContactMessage $contactMessage)
    {
        $this->messageService->delete($contactMessage->id);

        return $this->success('Message deleted successfully.');
    }
}
