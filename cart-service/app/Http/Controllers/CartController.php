<?php

namespace App\Http\Controllers;

use App\Http\Responses\ApiResponse;
use App\Repositories\Contracts\CartRepositoryInterface;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\ValidationException;

class CartController extends Controller {
    private CartRepositoryInterface $cartRepository;

    public function __construct(CartRepositoryInterface $cartRepository) {
        $this->cartRepository = $cartRepository;
    }

    public function get(): JsonResponse {
        $userId = Auth::id();
        $cart = $this->cartRepository->getCart($userId);
        return ApiResponse::success($cart, 'Carrinho obtido com sucesso.');
    }

    public function upsertItem(Request $request): JsonResponse {
        try {
            $this->validate($request, [
                'product_id' => 'required|string|uuid',
                'name' => 'required|string|max:255',
                'price' => 'required|numeric|min:0',
                'quantity' => 'required|integer|min:1',
            ]);

            $userId = Auth::id();
            $cart = $this->cartRepository->upsertItem($userId, $request->all());
            
            return ApiResponse::success($cart, 'Item adicionado ao carrinho.', 200);

        } catch (ValidationException $e) {
            return ApiResponse::error('Dados do item inválidos', 422, $e->errors());
        }
    }

    public function removeItem(string $productId): JsonResponse {
        $userId = Auth::id();
        $this->cartRepository->removeItem($userId, $productId);
        return ApiResponse::success(null, 'Item removido do carrinho.');
    }

    public function clearCart(): JsonResponse {
        $userId = Auth::id();
        $this->cartRepository->clearCart($userId);
        return ApiResponse::success(null, 'Carrinho esvaziado com sucesso.');
    }
}