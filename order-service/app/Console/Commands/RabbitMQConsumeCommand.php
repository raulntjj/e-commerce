<?php

namespace App\Console\Commands;

use App\Repositories\Contracts\ProductRepositoryInterface;
use App\Services\RabbitMQService;
use Illuminate\Console\Command;
use PhpAmqpLib\Message\AMQPMessage;

class RabbitMQConsumeCommand extends Command {
    protected $signature = 'rabbitmq:consume';
    protected $description = 'Consume messages from RabbitMQ';
    private RabbitMQService $rabbitMQService;

    public function __construct(RabbitMQService $rabbitMQService) {
        parent::__construct();
        $this->rabbitMQService = $rabbitMQService;
    }

    public function handle(ProductRepositoryInterface $productRepository): void {
        $queueName = 'product_events_queue';
        $routingKeys = ['product.*', 'order.created'];

        $this->info(" [*] Product worker is now listening for events. To exit press CTRL+C");

        $this->rabbitMQService->consume($queueName, $routingKeys, function (AMQPMessage $msg) use ($productRepository): void {
            $this->info("\n> Event Received: [" . $msg->getRoutingKey() . "]");
            $payload = json_decode($msg->body, true);

            switch ($msg->getRoutingKey()) {
                case 'order.created':
                    foreach ($payload['products'] as $product) {
                        $p = $productRepository->find($product['product_id']);
                        if ($p) {
                            $newStock = $p->stock - $product['quantity'];
                            $productRepository->update($p->uuid, ['stock' => $newStock]);
                            $this->info("   - Stock updated for product {$p->name}.");
                        }
                    }
                    break;
                case 'product.created':
                case 'product.updated':
                case 'product.deleted':
                     Log::info("AUDIT EVENT: '{$msg->getRoutingKey()}' | Payload: {$msg->body}");
                     $this->info("  - Event successfully audited.");
                    break;
            }
        });
    }
}