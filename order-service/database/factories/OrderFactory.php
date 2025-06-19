<?php

namespace Database\Factories;

use App\Models\Order;
use App\Models\OrderItem;
use Illuminate\Database\Eloquent\Factories\Factory;

class OrderFactory extends Factory {
    protected $model = Order::class;

    public function definition(): array {
        return [
            'user_id' => $this->faker->uuid,
            'total_amount' => $this->faker->randomFloat(2, 50, 500),
            'status' => 'pending_payment',
            'shipping_address_snapshot' => [
                'street' => $this->faker->streetAddress,
                'city' => $this->faker->city,
                'state' => $this->faker->stateAbbr,
                'zip_code' => $this->faker->postcode,
            ]
        ];
    }
    
    public function configure()
    {
        return $this->afterCreating(function (Order $order) {
            OrderItem::factory()->count($this->faker->numberBetween(1, 3))->create([
                'order_uuid' => $order->uuid,
            ]);
        });
    }
}