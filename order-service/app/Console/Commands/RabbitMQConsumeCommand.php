<?php

namespace App\Console\Commands;

use App\Listeners\PaymentSucceededListener;
use App\Repositories\Contracts\OrderRepositoryInterface;
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

    public function handle(OrderRepositoryInterface $orderRepository): void {
        $queueName = 'order_service_queue';
        $routingKeys = ['payment.succeeded', 'payment.failed']; 

        $this->info(" [*] Order worker is now listening for events. To exit press CTRL+C");

        $listener = new PaymentSucceededListener($orderRepository);

        $this->rabbitMQService->consume($queueName, $routingKeys, function (AMQPMessage $msg) use ($listener): void {
            $this->info("\n> Event Received: [" . $msg->getRoutingKey() . "]");
            $listener->handle($msg);
            $this->info("  - Event processed successfully.");
        });
    }
}