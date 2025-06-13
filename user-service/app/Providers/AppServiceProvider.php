<?php

namespace App\Providers;

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider {
    public function boot(): void {
        Blueprint::macro('userActions', function () {
            $this->uuid('created_by')->nullable();
            $this->uuid('updated_by')->nullable();
            $this->uuid('deleted_by')->nullable();
        });
    }
     
    public function register(): void {
        //
    }
}
