<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use App\Repositories\Contracts\OrderRepositoryInterface;
use App\Repositories\Eloquent\OrderRepository;

class RepositoryServiceProvider extends ServiceProvider {
    public function register(): void {
        $this->app->bind(
            OrderRepositoryInterface::class,
            OrderRepository::class
        );
    }
}