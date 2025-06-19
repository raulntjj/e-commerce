<?php

namespace Tests\Feature;

use App\Models\Order;
use App\Services\RabbitMQService;
use Illuminate\Support\Str;
use Laravel\Lumen\Testing\DatabaseMigrations;
use Mockery;
use Tests\TestCase;

class OrderControllerTest extends TestCase {
    use DatabaseMigrations;

    protected function setUp(): void {
        parent::setUp();
        $this->app->instance(RabbitMQService::class, Mockery::mock(RabbitMQService::class, function ($mock) {
            $mock->shouldReceive('publish')->andReturnNull();
        }));
    }
    
    /** @test */
    public function it_can_create_an_order() {
        $auth = $this->authenticate();

        $payload = [
            'items' => [
                ['product_id' => Str::uuid()->toString(), 'name' => 'Test Product 1', 'quantity' => 2, 'price' => 50.00],
                ['product_id' => Str::uuid()->toString(), 'name' => 'Test Product 2', 'quantity' => 1, 'price' => 100.00]
            ],
            'shipping_address' => [
                'street' => '123 Main St', 'city' => 'Anytown', 'state' => 'CA', 'zip_code' => '90210'
            ],
        ];

        $this->post('/orders', $payload, $auth['headers']);

        $this->assertResponseStatus(201);
        $this->seeInDatabase('orders', ['user_id' => $auth['userId'], 'total_amount' => 200.00]);
        $this->seeInDatabase('order_items', ['product_name' => 'Test Product 1', 'quantity' => 2]);
        $this->seeInDatabase('order_items', ['product_name' => 'Test Product 2', 'quantity' => 1]);
        $this->seeJsonContains(['status' => 'pending_payment']);
    }

    /** @test */
    public function it_can_get_a_specific_order_belonging_to_the_user() {
        $auth = $this->authenticate();
        $order = Order::factory()->create(['user_id' => $auth['userId']]);

        $this->get('/orders/' . $order->uuid, $auth['headers']);

        $this->assertResponseOk();
        $this->seeJson(['uuid' => $order->uuid, 'user_id' => $auth['userId']]);
        $this->seeJsonStructure(['data' => ['uuid', 'user_id', 'items' => []]]);
    }
    
    /** @test */
    public function it_cannot_get_an_order_belonging_to_another_user() {
        $auth = $this->authenticate();
        $otherOrder = Order::factory()->create(['user_id' => 'another-user-uuid']);

        $this->get('/orders/' . $otherOrder->uuid, $auth['headers']);

        $this->assertResponseStatus(404);
        $this->seeJson(['message' => 'Pedido não encontrado']);
    }
}