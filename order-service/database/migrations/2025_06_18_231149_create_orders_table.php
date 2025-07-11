<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('orders', function (Blueprint $table) {
            $table->uuid('uuid')->primary();
            $table->uuid('user_id');
            $table->decimal('total_amount', 10, 2);
            $table->string('status')->default('pending_payment');
            $table->json('shipping_address_snapshot');
            $table->timestamps();
            $table->softDeletes();
            $table->userActions();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('orders');
    }
};