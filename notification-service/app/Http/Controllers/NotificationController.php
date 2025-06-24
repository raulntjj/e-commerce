<?php

namespace App\Http\Controllers;

use App\Http\Responses\ApiResponse;
use App\Repositories\Contracts\NotificationRepositoryInterface;
use Auth;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class NotificationController extends Controller {
    private NotificationRepositoryInterface $notificationRepository;

    public function __construct(NotificationRepositoryInterface $notificationRepository) {
        $this->notificationRepository = $notificationRepository;
    }

    public function getByUser(Request $request): JsonResponse {
        $userId = Auth::id();
        $notifications = $this->notificationRepository->findByUser($userId);
        return ApiResponse::success($notifications, 'Notificações listadas com sucesso.');
    }

    public function markAsRead(Request $request, $id): JsonResponse {
        $notification = $this->notificationRepository->markAsRead($id);
        
        if (!$notification) {
            return ApiResponse::error('Notificação não encontrada', 404);
        }

        $authenticatedUserId = Auth::id();

        if ($notification->user_id !== $authenticatedUserId) {
             return ApiResponse::error('Não autorizado', 403);
        }

        return ApiResponse::success($notification, 'Notificação marcada como lida.');
    }
}