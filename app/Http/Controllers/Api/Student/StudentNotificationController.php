<?php

namespace App\Http\Controllers\Api\Student;

use App\Models\Announcement;
use App\Models\ImportantMessage;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class StudentNotificationController extends BaseStudentApiController
{
    /**
     * Get Flash Alerts and Announcements
     */
    public function index(Request $request): JsonResponse
    {
        // 1. Flash / Important Alerts
        $flashAlerts = ImportantMessage::active()
            ->get()
            ->map(function ($msg) {
                return [
                    'id' => $msg->id,
                    'type' => 'flash_alert',
                    'category' => $msg->type ?? 'info', // danger, warning, info, success
                    'title' => $msg->title,
                    'message' => $msg->message,
                    'action_text' => $msg->action_text,
                    'action_url' => $msg->action_url ? url($msg->action_url) : null,
                    'created_at' => $msg->created_at ? $msg->created_at->format('d M, Y h:i A') : null,
                ];
            });

        // 2. School/College Announcements
        $announcements = Announcement::active()
            ->latest('id')
            ->paginate(15);

        $formattedAnnouncements = collect($announcements->items())->map(function ($item) {
            return [
                'id' => $item->id,
                'type' => 'announcement',
                'title' => $item->title,
                'content' => $item->content,
                'badge' => $item->badge ?? 'Notice',
                'link_url' => $item->link_url ? url($item->link_url) : null,
                'link_text' => $item->link_text,
                'created_at' => $item->created_at ? $item->created_at->format('d M, Y h:i A') : null,
            ];
        });

        $data = [
            'flash_alerts' => $flashAlerts,
            'announcements' => $formattedAnnouncements,
            'pagination' => [
                'current_page' => $announcements->currentPage(),
                'last_page' => $announcements->lastPage(),
                'total' => $announcements->total(),
            ],
        ];

        return $this->success('Notifications and alerts fetched successfully.', $data);
    }
}
