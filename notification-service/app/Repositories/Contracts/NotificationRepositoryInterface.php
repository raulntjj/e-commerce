<?php

namespace App\Repositories\Contracts;

use App\Models\Notification;
use Illuminate\Support\Collection;

interface NotificationRepositoryInterface {
    public function create(array $data): Notification;
    public function findByUser(string $userId): Collection;
    public function markAsRead(string $notificationId): ?Notification;
}