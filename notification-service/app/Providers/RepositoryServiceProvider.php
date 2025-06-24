<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use App\Repositories\Contracts\NotificationRepositoryInterface;
use App\Repositories\Eloquent\NotificationRepository;

class RepositoryServiceProvider extends ServiceProvider {
    public function register(): void {
        $this->app->bind(
            NotificationRepositoryInterface::class,
            NotificationRepository::class
        );
    }
}