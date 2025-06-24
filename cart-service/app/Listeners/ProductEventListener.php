<?php

namespace App\Listeners;

use App\Repositories\Contracts\CartRepositoryInterface;
use Illuminate\Support\Facades\Log;
use PhpAmqpLib\Message\AMQPMessage;

class ProductEventListener {
    private CartRepositoryInterface $cartRepository;

    public function __construct(CartRepositoryInterface $cartRepository) {
        $this->cartRepository = $cartRepository;
    }

    public function handle(AMQPMessage $msg): void {
        $payload = json_decode($msg->body, true);
        $routingKey = $msg->getRoutingKey();

        if ($routingKey === 'product.updated') {
            $this->handleProductUpdate($payload);
        }
    }

    private function handleProductUpdate(array $productData): void {
        $productId = $productData['uuid'];
        Log::info("Processing product.updated event for product: {$productId}");

        // Encontra todos os usuários que têm este produto no carrinho
        $affectedUserIds = $this->cartRepository->getUsersWithProduct($productId);

        if (empty($affectedUserIds)) {
            Log::info("No carts to update for product: {$productId}");
            return;
        }

        Log::info("Found " . count($affectedUserIds) . " carts to update for product: {$productId}");

        // Atualiza o item em cada carrinho
        foreach ($affectedUserIds as $userId) {
            $item = $this->cartRepository->getCartItem($userId, $productId);
            if ($item) {
                // Atualiza os dados do item com o payload do evento
                $item['name'] = $productData['name'];
                $item['price'] = (float) $productData['price'];
                
                // Salva o item atualizado de volta no carrinho do usuário
                $this->cartRepository->addItem($userId, $item);
                Log::info("Updated product {$productId} in cart for user {$userId}");
            }
        }
    }
}