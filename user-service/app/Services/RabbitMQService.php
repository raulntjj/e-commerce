<?php

namespace App\Services;

use PhpAmqpLib\Connection\AMQPStreamConnection;
use PhpAmqpLib\Message\AMQPMessage;

class RabbitMQService {
    protected $connection;
    protected $channel;

    public function __construct() {
        $host = config('services.rabbitmq.host');
        $port = config('services.rabbitmq.port');
        $user = config('services.rabbitmq.user');
        $pass = config('services.rabbitmq.pass');
        $this->connection = new AMQPStreamConnection($host, $port, $user, $pass);
        $this->channel = $this->connection->channel();
    }

    public function publish($routingKey, $message): void {
        $exchange = 'ecommerce_events';
        $this->channel->exchange_declare($exchange, 'topic', false, true, false);

        $msg = new AMQPMessage(json_encode($message), ['delivery_mode' => AMQPMessage::DELIVERY_MODE_PERSISTENT]);
        $this->channel->basic_publish($msg, $exchange, $routingKey);
    }

    public function consume(string $queueName, string $routingKey, \Closure $callback): void {
        $exchange = 'ecommerce_events';
        $this->channel->exchange_declare($exchange, 'topic', false, true, false);
        $this->channel->queue_declare($queueName, false, true, false, false);
        $this->channel->queue_bind($queueName, $exchange, $routingKey);

        echo " [*] Waiting for messages for routing key '$routingKey'. To exit press CTRL+C\n";

        $this->channel->basic_consume($queueName, '', false, false, false, false, function (AMQPMessage $msg) use ($callback) {
            $callback($msg);
            $msg->ack();
        });

        while ($this->channel->is_consuming()) {
            $this->channel->wait();
        }
    }

    public function __destruct() {
        $this->channel->close();
        $this->connection->close();
    }
}