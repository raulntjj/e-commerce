<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use App\Repositories\Contracts\UserRepositoryInterface;
use App\Repositories\Eloquent\UserRepository;

class RepositoryServiceProvider extends ServiceProvider {
    public function register(): void {
        $this->app->bind(
            UserRepositoryInterface::class,
            UserRepository::class
        );
    }
}