<?php

namespace App\Console\Commands;

use App\Listeners\AuditUserActivityListener;
use App\Repositories\Contracts\ProductRepositoryInterface;
use App\Services\RabbitMQService;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Log;
use PhpAmqpLib\Message\AMQPMessage;

class RabbitMQConsumeCommand extends Command {
    protected $signature = 'rabbitmq:consume';
    protected $description = 'Consume messages from RabbitMQ for product service';
    private RabbitMQService $rabbitMQService;

    public function __construct(RabbitMQService $rabbitMQService)
    {
        parent::__construct();
        $this->rabbitMQService = $rabbitMQService;
    }

    public function handle(ProductRepositoryInterface $productRepository): void
    {
        $queueName = 'product_service_queue';
        $routingKeys = ['product.*', 'order.created'];

        $this->info(" [*] Product worker is now listening for events. To exit press CTRL+C");

        $auditListener = new AuditUserActivityListener();

        $this->rabbitMQService->consume($queueName, $routingKeys, function (AMQPMessage $msg) use ($productRepository, $auditListener): void {
            $this->info("\n> Event Received: [" . $msg->getRoutingKey() . "]");
            $payload = json_decode($msg->body, true);

            switch ($msg->getRoutingKey()) {
                case 'order.created':
                    if (isset($payload['items'])) {
                        foreach ($payload['items'] as $itemData) {
                            $product = $productRepository->find($itemData['product_uuid']);
                            if ($product) {
                                $newStock = $product->stock - $itemData['quantity'];
                                $productRepository->update($product->uuid, ['stock' => $newStock]);
                                $this->info("   - Stock updated for product {$product->name}. New stock: {$newStock}");
                            } else {
                                Log::warning("Product with ID {$itemData['product_uuid']} not found. Could not update stock.");
                            }
                        }
                    }
                    break;

                case 'product.created':
                case 'product.updated':
                case 'product.deleted':
                    $auditListener->handle($msg);
                    $this->info("  - Event successfully audited.");
                    break;
            }
        });
    }
}