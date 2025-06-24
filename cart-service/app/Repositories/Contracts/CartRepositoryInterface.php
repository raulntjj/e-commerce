<?php

namespace App\Repositories\Contracts;

interface CartRepositoryInterface {
    public function getCart(string $userId): array;
    public function upsertItem(string $userId, array $itemData): array;
    public function updateItemQuantity(string $userId, string $productId, int $quantity): array;
    public function removeItem(string $userId, string $productId): bool;
    public function clearCart(string $userId): bool;
    public function getCartItem(string $userId, string $productId): ?array;
    public function getUsersWithProduct(string $productId): array;
}