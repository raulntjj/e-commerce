<?php

namespace App\Http\Controllers;

use App\Http\Responses\ApiResponse;
use App\Repositories\Contracts\OrderRepositoryInterface;
use App\Services\RabbitMQService;
use Auth;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Validation\ValidationException;

class OrderController extends Controller {
    private OrderRepositoryInterface $orderRepository;
    private RabbitMQService $rabbitMQService;

    public function __construct(
        OrderRepositoryInterface $orderRepository,
        RabbitMQService $rabbitMQService
    ) {
        $this->orderRepository = $orderRepository;
        $this->rabbitMQService = $rabbitMQService;
    }

    public function getAll(): JsonResponse {
        $orders = $this->orderRepository->all();
        return ApiResponse::success($orders, 'Pedidos obtidos com sucesso', 201);
    }

    public function create(Request $request): JsonResponse {
        try {
            $this->validate($request, [
                'products' => 'required|array',
                'products.*.product_id' => 'required|string',
                'products.*.quantity' => 'required|integer|min:1',
                'products.*.price' => 'required|numeric|min:0',
                'shipping_address' => 'required|array',
                'total_amount' => 'required|numeric'
            ]);

            $payload = $request->all();
            $payload['user_id'] = Auth::user()->uuid;
            $payload['status'] = 'pending_payment';

            $order = $this->orderRepository->create($payload);

            $this->rabbitMQService->publish('order.created', $order->toArray());

            return ApiResponse::success($order, 'Pedido criado com sucesso.', 201);
        } catch (ValidationException $e) {
            return ApiResponse::error('Dados do pedido inválidos', 422, $e->errors());
        }
    }

    public function get($id, Request $request): JsonResponse {
        $order = $this->orderRepository->find($id);
        
        // Ensure user can only see their own order
        if (!$order || $order->user_id !== $request->auth->sub) {
            return ApiResponse::error('Pedido não encontrado', 404);
        }
        
        return ApiResponse::success($order);
    }
}