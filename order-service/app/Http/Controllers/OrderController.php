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

    public function getAll(Request $request): JsonResponse {
        $orders = $this->orderRepository->all();
        return ApiResponse::success($orders, 'Pedidos obtidos com sucesso');
    }

    public function create(Request $request): JsonResponse {
        try {
            $this->validate($request, [
                'items' => 'required|array|min:1',
                'items.*.product_id' => 'required|string|uuid',
                'items.*.name' => 'required|string|max:255',
                'items.*.quantity' => 'required|integer|min:1',
                'items.*.price' => 'required|numeric|min:0',
                'shipping_address' => 'required|array',
                'shipping_address.street' => 'required|string',
                'shipping_address.city' => 'required|string',
                'shipping_address.state' => 'required|string|size:2',
                'shipping_address.zip_code' => 'required|string',
            ]);

            $payload = $request->all();
            
            $totalAmount = array_reduce($payload['items'], function ($sum, $item) {
                return $sum + ($item['quantity'] * $item['price']);
            }, 0);
            
            $payload['user_id'] = Auth::id();
            $payload['status'] = 'pending_payment';
            $payload['total_amount'] = $totalAmount;

            $order = $this->orderRepository->create($payload);

            $this->rabbitMQService->publish('order.created', $order->toArray());

            return ApiResponse::success($order, 'Pedido criado com sucesso.', 201);
        } catch (ValidationException $e) {
            return ApiResponse::error('Dados do pedido inválidos', 422, $e->errors());
        }
    }

    public function get($id, Request $request): JsonResponse {
        $order = $this->orderRepository->find($id);
        
        if (!$order || $order->user_id !== $request->auth->sub) {
            return ApiResponse::error('Pedido não encontrado', 404);
        }
        
        return ApiResponse::success($order);
    }
}