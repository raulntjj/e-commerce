<?php

namespace App\Repositories\Eloquent;

use App\Models\Notification;
use App\Repositories\Contracts\NotificationRepositoryInterface;
use Illuminate\Support\Collection;

class NotificationRepository implements NotificationRepositoryInterface {
    protected Notification $model;

    public function __construct(Notification $model) {
        $this->model = $model;
    }

    public function create(array $data): Notification {
        return $this->model->create($data);
    }

    public function findByUser(string $userId): Collection {
        return $this->model->where('user_id', $userId)->latest()->get();
    }
    
    public function markAsRead(string $notificationId): ?Notification {
        $notification = $this->model->find($notificationId);
        if ($notification) {
            $notification->update(['read_at' => now()]);
            return $notification;
        }
        return null;
    }
}