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
        
        if (empty($payload['order_id'])) {
            Log::warning("Received payment.succeeded event with no order_id.");
            return;
        }

        $order = $this->orderRepository->update($payload['order_id'], ['status' => 'paid']);

        if ($order) {
            Log::info("Order {$order->uuid} status updated to 'paid'.");
        } else {
            Log::error("Failed to update status for order {$payload['order_id']}.");
        }
    }
}