<?php

namespace App\Console\Commands;

use App\Listeners\AuditUserActivityListener;
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

    public function handle(): void {
        $queueName = 'user_audit_queue';
        $routingKey = 'user.*';
    
        $listener = new AuditUserActivityListener();
    
        $this->info(" [*] User audit worker is now listening for routing key '$routingKey'. To exit press CTRL+C");
    
        $this->rabbitMQService->consume($queueName, $routingKey, function (AMQPMessage $msg) use ($listener): void {
            $this->line("\n> User Event Received: [" . $msg->getRoutingKey() . "]");
            $listener->handle($msg);
            $this->info("  - Event successfully audited.");
        });
    }
}