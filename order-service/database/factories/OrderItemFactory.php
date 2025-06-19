<?php

namespace Database\Factories;

use App\Models\OrderItem;
use Illuminate\Database\Eloquent\Factories\Factory;

class OrderItemFactory extends Factory {
    protected $model = OrderItem::class;

    public function definition(): array {
        $quantity = $this->faker->numberBetween(1, 5);
        $price = $this->faker->randomFloat(2, 10, 200);

        return [
            'order_uuid' => $this->faker->uuid,
            'product_uuid' => $this->faker->uuid,
            'product_name' => $this->faker->words(3, true),
            'quantity' => $quantity,
            'price' => $price,
            'total' => $quantity * $price,
        ];
    }
}