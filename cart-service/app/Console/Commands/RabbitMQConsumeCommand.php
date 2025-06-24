<?php

namespace App\Console\Commands;

use App\Listeners\ProductEventListener;
use App\Repositories\Contracts\CartRepositoryInterface;
use App\Services\RabbitMQService;
use Illuminate\Console\Command;
use PhpAmqpLib\Message\AMQPMessage;

class RabbitMQConsumeCommand extends Command {
    protected $signature = 'rabbitmq:consume';
    protected $description = 'Consume messages from RabbitMQ for cart service';

    private RabbitMQService $rabbitMQService;

    public function __construct(RabbitMQService $rabbitMQService) {
        parent::__construct();
        $this->rabbitMQService = $rabbitMQService;
    }

    public function handle(CartRepositoryInterface $cartRepository): void {
        $queueName = 'cart_service_queue';
        // O serviço de carrinho só precisa ouvir por atualizações de produtos
        $routingKeys = ['product.updated']; 

        $this->info(" [*] Cart worker is now listening for events. To exit press CTRL+C");

        $listener = new ProductEventListener($cartRepository);

        $this->rabbitMQService->consume($queueName, $routingKeys, function (AMQPMessage $msg) use ($listener) {
            $this->info("\n> Event Received: [" . $msg->getRoutingKey() . "]");
            $listener->handle($msg);
            $this->info("  - Event processed successfully.");
        });
    }
}