<?php

namespace App\Console\Commands;

use App\Listeners\PaymentSucceededListener;
use App\Repositories\Contracts\OrderRepositoryInterface;
use App\Services\RabbitMQService;
use Illuminate\Console\Command;
use PhpAmqpLib\Message\AMQPMessage;

class RabbitMQConsumeCommand extends Command {
    protected $signature = 'rabbitmq:consume';
    protected $description = 'Consume order-related messages from RabbitMQ';
    private RabbitMQService $rabbitMQService;

    public function __construct(RabbitMQService $rabbitMQService) {
        parent::__construct();
        $this->rabbitMQService = $rabbitMQService;
    }

    public function handle(OrderRepositoryInterface $orderRepository): void {
        $queueName = 'order_events_queue';
        $routingKeys = [
            'payment.succeeded',
            'payment.failed'
        ];

        $this->info(" [*] Order worker listening for events. To exit press CTRL+C");

        $this->rabbitMQService->consume($queueName, $routingKeys, function (AMQPMessage $msg) use ($orderRepository): void {
            $this->info("\n> Event Received: [" . $msg->getRoutingKey() . "]");
            
            switch ($msg->getRoutingKey()) {
                case 'payment.succeeded':
                    (new PaymentSucceededListener($orderRepository))->handle($msg);
                    $this->info("  - Order status updated based on payment.");
                    break;
            }
        });
    }
}