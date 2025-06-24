<?php
namespace App\Listeners;

use App\Repositories\Contracts\NotificationRepositoryInterface;
use PhpAmqpLib\Message\AMQPMessage;
use Illuminate\Support\Facades\Log;

class NotificationListener {
    private NotificationRepositoryInterface $notificationRepository;

    public function __construct(NotificationRepositoryInterface $notificationRepository) {
        $this->notificationRepository = $notificationRepository;
    }

    public function handle(AMQPMessage $msg): void {
        $payload = json_decode($msg->body, true);
        $routingKey = $msg->getRoutingKey();
        
        $notificationData = $this->prepareNotificationData($routingKey, $payload);

        if ($notificationData) {
            $this->notificationRepository->create($notificationData);
            Log::info("Notification created for event '{$routingKey}'.");
        } else {
            Log::warning("No notification handler for routing key '{$routingKey}'.");
        }
    }

    private function prepareNotificationData(string $routingKey, array $payload): ?array {
        $userId = $payload['user_id'] ?? $payload['uuid'] ?? null;
        if (!$userId) return null;

        $message = match ($routingKey) {
            'order.created' => "Seu pedido #{$payload['uuid']} foi criado com sucesso!",
            'payment.succeeded' => "O pagamento para o pedido #{$payload['order_id']} foi aprovado!",
            'payment.failed' => "O pagamento para o pedido #{$payload['order_id']} falhou.",
            'user.created' => "Bem-vindo! Sua conta foi criada com sucesso.",
            default => null,
        };

        if (!$message) return null;

        return [
            'user_id' => $userId,
            'type' => $routingKey,
            'message' => $message,
        ];
    }
}