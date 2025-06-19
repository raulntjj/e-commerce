<?php
namespace App\Listeners;

use App\Repositories\Contracts\OrderRepositoryInterface;
use PhpAmqpLib\Message\AMQPMessage;
use Illuminate\Support\Facades\Log;

class PaymentSucceededListener {
    private OrderRepositoryInterface $orderRepository;

    public function __construct(OrderRepositoryInterface $orderRepository) {
        $this->orderRepository = $orderRepository;
    }

    public function handle(AMQPMessage $msg): void {
        $payload = json_decode($msg->body, true);
        $routingKey = $msg->getRoutingKey();
        
        if (empty($payload['order_id'])) {
            Log::warning("Received {$routingKey} event with no order_id.");
            return;
        }

        $newStatus = match ($routingKey) {
            'payment.succeeded' => 'paid',
            'payment.failed' => 'payment_failed',
            default => null,
        };

        if (!$newStatus) {
            Log::warning("Unknown routing key '{$routingKey}' received.");
            return;
        }

        $order = $this->orderRepository->update($payload['order_id'], ['status' => $newStatus]);

        if ($order) {
            Log::info("Order {$order->uuid} status updated to '{$newStatus}'.");
        } else {
            Log::error("Failed to update status for order {$payload['order_id']}.");
        }
    }
}