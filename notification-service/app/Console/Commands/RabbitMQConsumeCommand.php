<?php

namespace App\Console\Commands;

use App\Listeners\NotificationListener;
use App\Repositories\Contracts\NotificationRepositoryInterface;
use App\Services\RabbitMQService;
use Illuminate\Console\Command;
use PhpAmqpLib\Message\AMQPMessage;

class RabbitMQConsumeCommand extends Command {
    protected $signature = 'rabbitmq:consume';
    protected $description = 'Consume messages from RabbitMQ for notification service';
    
    private RabbitMQService $rabbitMQService;

    public function __construct(RabbitMQService $rabbitMQService) {
        parent::__construct();
        $this->rabbitMQService = $rabbitMQService;
    }

    public function handle(NotificationRepositoryInterface $notificationRepository): void {
        $queueName = 'notification_service_queue';
        // Lista de eventos que este serviço deve ouvir
        $routingKeys = ['order.created', 'payment.succeeded', 'payment.failed', 'user.created'];

        $this->info(" [*] Notification worker is now listening for events. To exit press CTRL+C");

        $listener = new NotificationListener($notificationRepository);

        $this->rabbitMQService->consume($queueName, $routingKeys, function (AMQPMessage $msg) use ($listener): void {
            $this->info("\n> Event Received: [" . $msg->getRoutingKey() . "]");
            $listener->handle($msg);
            $this->info("  - Event processed successfully.");
        });
    }
}