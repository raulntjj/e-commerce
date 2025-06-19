<?php

namespace Tests\Feature;

use App\Models\Product;
use App\Services\RabbitMQService;
use Laravel\Lumen\Testing\DatabaseMigrations;
use Mockery;
use Tests\TestCase;

class ProductControllerTest extends TestCase {
    use DatabaseMigrations;

    protected function setUp(): void {
        parent::setUp();
        // Mock RabbitMQService para não enviar mensagens reais durante os testes
        $this->app->instance(RabbitMQService::class, Mockery::mock(RabbitMQService::class, function ($mock) {
            $mock->shouldReceive('publish')->andReturnNull();
        }));
    }

    /** @test */
    public function it_can_create_a_product() {
        $auth = $this->authenticate();

        $payload = [
            'name' => 'Super Product',
            'description' => 'A very cool product.',
            'price' => 99.99,
            'stock' => 100
        ];

        $this->post('/products', $payload, $auth['headers']);
        
        $this->assertResponseStatus(201);
        $this->seeInDatabase('products', ['name' => 'Super Product']);
        $this->seeJsonContains(['name' => 'Super Product']);
    }

    /** @test */
    public function it_can_get_all_products() {
        $auth = $this->authenticate();
        Product::factory()->count(3)->create();

        $this->get('/products', $auth['headers']);

        $this->assertResponseOk();
        $response = json_decode($this->response->getContent(), true);
        $this->assertCount(3, $response['data']);
    }

    /** @test */
    public function it_can_get_a_specific_product() {
        $auth = $this->authenticate();
        $product = Product::factory()->create();

        $this->get('/products/' . $product->uuid, $auth['headers']);

        $this->assertResponseOk();
        $this->seeJson(['uuid' => $product->uuid, 'name' => $product->name]);
    }
    
    /** @test */
    public function it_can_update_a_product() {
        $auth = $this->authenticate();
        $product = Product::factory()->create();
        $payload = ['name' => 'Updated Product Name'];

        $this->put('/products/' . $product->uuid, $payload, $auth['headers']);

        $this->assertResponseOk();
        $this->seeInDatabase('products', ['uuid' => $product->uuid, 'name' => 'Updated Product Name']);
        $this->seeJson(['name' => 'Updated Product Name']);
    }

    /** @test */
    public function it_can_delete_a_product() {
        $auth = $this->authenticate();
        $product = Product::factory()->create();

        $this->delete('/products/' . $product->uuid, $auth['headers']);

        $this->assertResponseOk();
        $this->seeJson(['message' => 'Produto deletado com sucesso.']);
        $this->assertNotNull(Product::withTrashed()->find($product->uuid)->deleted_at);
    }
}