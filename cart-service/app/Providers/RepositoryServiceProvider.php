<?php

namespace App\Providers;

use App\Repositories\Contracts\CartRepositoryInterface;
use App\Repositories\Redis\CartRepository;
use Illuminate\Support\ServiceProvider;

class RepositoryServiceProvider extends ServiceProvider {
    public function register(): void
    {
        $this->app->bind(
            CartRepositoryInterface::class,
            CartRepository::class
        );
    }
}