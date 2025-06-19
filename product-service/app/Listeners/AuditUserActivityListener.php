<?php

namespace App\Listeners;

use Illuminate\Support\Facades\Log;
use PhpAmqpLib\Message\AMQPMessage;

class AuditUserActivityListener {
    public function handle(AMQPMessage $msg): void
    {
        $eventType = $msg->getRoutingKey();
        $payload = $msg->body;
        Log::info("AUDIT EVENT: '{$eventType}' | Payload: {$payload}");
    }
}