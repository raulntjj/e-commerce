<?php

namespace App\Providers;

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     *
     * @return void
     */
    public function register()
    {
        //
    }

    /**
     * Boot any application services.
     *
     * @return void
     */
    public function boot(): void {
        Blueprint::macro('userActions', function (): void {
            $this->uuid('created_by')->nullable();
            $this->uuid('updated_by')->nullable();
            $this->uuid('deleted_by')->nullable();
        });
    }
}