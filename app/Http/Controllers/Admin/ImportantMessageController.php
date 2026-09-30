<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\BaseController;
use App\Http\Requests\Admin\ImportantMessageRequest;
use App\Models\ImportantMessage;
use App\Services\Admin\ImportantMessageService;
use Illuminate\Http\Request;

class ImportantMessageController extends BaseController
{
    public function __construct(
        protected ImportantMessageService $messageService
    ) {}

    public function index()
    {
        return view('admin.website.important_messages.index');
    }

    public function list(Request $request)
    {
        $filters = [
            'search' => $request->input('search.value'),
            'type' => $request->input('type'),
            'filter_status' => $request->input('filter_status'),
        ];

        $length = max((int) $request->input('length', 10), 1);
        $start = max((int) $request->input('start', 0), 0);
        $page = (int) floor($start / $length) + 1;

        $orderColumn = $request->input('order.0.column');
        $orderDirection = $request->input('order.0.dir', 'asc');

        $messages = $this->messageService->getMessages(
            $filters,
            $length,
            $page,
            $orderColumn !== null ? (int) $orderColumn : null,
            $orderDirection
        );

        return $this->datatable($messages, (int) $request->input('draw', 1));
    }

    public function store(ImportantMessageRequest $request)
    {
        $this->messageService->create($request->validated());

        return $this->success('Important message created successfully.');
    }

    public function edit(ImportantMessage $important_message)
    {
        return $this->success('Important message fetched successfully.', [
            'id' => $important_message->id,
            'title' => $important_message->title,
            'message' => $important_message->message,
            'type' => $important_message->type,
            'action_text' => $important_message->action_text,
            'action_url' => $important_message->action_url,
            'start_date' => $important_message->start_date ? $important_message->start_date->format('Y-m-d\TH:i') : null,
            'end_date' => $important_message->end_date ? $important_message->end_date->format('Y-m-d\TH:i') : null,
            'sort_order' => $important_message->sort_order,
            'status' => $important_message->status,
        ]);
    }

    public function update(ImportantMessageRequest $request, ImportantMessage $important_message)
    {
        $this->messageService->update(
            $important_message->id,
            $request->validated()
        );

        return $this->success('Important message updated successfully.');
    }

    public function destroy(ImportantMessage $important_message)
    {
        $this->messageService->delete($important_message->id);

        return $this->success('Important message deleted successfully.');
    }

    public function changeStatus(ImportantMessage $important_message)
    {
        $this->messageService->changeStatus($important_message->id);

        return $this->success('Message status updated successfully.');
    }
}
