<?php

namespace App\Repositories\Redis;

use App\Repositories\Contracts\CartRepositoryInterface;

class CartRepository implements CartRepositoryInterface {
    private const CART_KEY_PREFIX = 'cart:';
    private const PRODUCT_CARTS_KEY_PREFIX = 'product_carts:';

    private \Redis $redis;

    public function __construct(\Redis $redis) {
        $this->redis = $redis;
    }

    private function getCartKey(string $userId): string {
        return self::CART_KEY_PREFIX . $userId;
    }

    private function getProductCartsKey(string $productId): string {
        return self::PRODUCT_CARTS_KEY_PREFIX . $productId;
    }

    public function getCart(string $userId): array {
        $cartItems = $this->redis->hGetAll($this->getCartKey($userId));
        $items = [];
        $total = 0.0;

        foreach ($cartItems as $productId => $itemJson) {
            $item = json_decode($itemJson, true);
            $items[] = $item;
            $total += $item['price'] * $item['quantity'];
        }

        return [
            'items' => $items,
            'total_amount' => round($total, 2),
        ];
    }


    public function upsertItem(string $userId, array $itemData): array {
        $cartKey = $this->getCartKey($userId);
        $productId = $itemData['product_id'];

        $item = [
            'product_id' => $productId,
            'name' => $itemData['name'],
            'price' => (float) $itemData['price'],
            'quantity' => (int) $itemData['quantity'],
        ];

        $this->redis->hSet($cartKey, $productId, json_encode($item));
        $this->redis->sAdd($this->getProductCartsKey($productId), $userId);

        return $this->getCart($userId);
    }
    
    public function updateItemQuantity(string $userId, string $productId, int $quantity): array {
        $cartKey = $this->getCartKey($userId);
        $itemJson = $this->redis->hGet($cartKey, $productId);

        if (!$itemJson) {
            return $this->getCart($userId);
        }

        $item = json_decode($itemJson, true);
        $item['quantity'] = $quantity;

        $this->redis->hSet($cartKey, $productId, json_encode($item));

        return $this->getCart($userId);
    }

    public function removeItem(string $userId, string $productId): bool {
        $result = (bool) $this->redis->hDel($this->getCartKey($userId), $productId);
        
        if ($result) {
            $this->redis->sRem($this->getProductCartsKey($productId), $userId);
        }
        
        return $result;
    }

    public function clearCart(string $userId): bool {
        $cartKey = $this->getCartKey($userId);
        $cartItems = $this->redis->hGetAll($cartKey);

        foreach (array_keys($cartItems) as $productId) {
            $this->redis->sRem($this->getProductCartsKey($productId), $userId);
        }

        return (bool) $this->redis->del($cartKey);
    }

    public function getCartItem(string $userId, string $productId): ?array {
        $itemJson = $this->redis->hGet($this->getCartKey($userId), $productId);
        return $itemJson ? json_decode($itemJson, true) : null;
    }

    public function getUsersWithProduct(string $productId): array {
        return $this->redis->sMembers($this->getProductCartsKey($productId));
    }
}