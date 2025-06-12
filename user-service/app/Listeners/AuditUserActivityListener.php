<?php

namespace App\Listeners;

use PhpAmqpLib\Message\AMQPMessage;
use Illuminate\Support\Facades\Log;

class AuditUserActivityListener {
    public function handle(AMQPMessage $msg): void {
        $eventType = $msg->getRoutingKey();
        $payload = $msg->body;
        Log::info("AUDIT EVENT: '{$eventType}' | Payload: {$payload}");
    }
}