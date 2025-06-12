<?php

namespace App\Services;

use PhpAmqpLib\Connection\AMQPStreamConnection;
use PhpAmqpLib\Message\AMQPMessage;

class RabbitMQService {
    protected $connection;
    protected $channel;

    public function __construct() {
        $host = env('RABBITMQ_HOST', 'rabbitmq');
        $port = 5672;
        $user = env('RABBITMQ_DEFAULT_USER', 'root');
        $pass = env('RABBITMQ_DEFAULT_PASS', 'root');
        
        $this->connection = new AMQPStreamConnection($host, $port, $user, $pass);
        $this->channel = $this->connection->channel();
    }

    public function publish($routingKey, $message) {
        $exchange = 'ecommerce_events';
        $this->channel->exchange_declare($exchange, 'topic', false, true, false);

        $msg = new AMQPMessage(json_encode($message), ['delivery_mode' => AMQPMessage::DELIVERY_MODE_PERSISTENT]);
        $this->channel->basic_publish($msg, $exchange, $routingKey);
    }

    public function __destruct() {
        $this->channel->close();
        $this->connection->close();
    }
}