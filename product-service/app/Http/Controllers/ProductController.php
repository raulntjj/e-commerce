<?php

namespace App\Http\Controllers;

use App\Http\Responses\ApiResponse;
use App\Repositories\Contracts\ProductRepositoryInterface;
use App\Services\RabbitMQService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Validation\ValidationException;

class ProductController extends Controller {
    private ProductRepositoryInterface $productRepository;
    private RabbitMQService $rabbitMQService;

    public function __construct(
        ProductRepositoryInterface $productRepository,
        RabbitMQService $rabbitMQService
    ) {
        $this->productRepository = $productRepository;
        $this->rabbitMQService = $rabbitMQService;
    }

    public function getAll(): JsonResponse {
        return ApiResponse::success($this->productRepository->all(), 'Produtos listados com sucesso.');
    }

    public function get($id): JsonResponse {
        $product = $this->productRepository->find($id);
        return $product
            ? ApiResponse::success($product)
            : ApiResponse::error('Produto não encontrado', 404);
    }

    public function create(Request $request): JsonResponse {
        try {
            $product = $this->productRepository->create($request->all());
           
            $this->rabbitMQService->publish('product.created', $product->toArray());

            return ApiResponse::success($product, 'Produto criado com sucesso.', 201);
        } catch (ValidationException $e) {
            return ApiResponse::error('Dados inválidos', 422, $e->errors());
        }
    }

    public function update(Request $request, $id): JsonResponse {
        $updatedProduct = $this->productRepository->update($id, $request->all());

        $this->rabbitMQService->publish('product.updated', $updatedProduct->toArray());

        return ApiResponse::success($updatedProduct, 'Produto atualizado com sucesso.');
       
    }

    public function delete($id): JsonResponse {
        $product = $this->productRepository->find($id);
        if (!$product) {
            return ApiResponse::error('Produto não encontrado', 404);
        }
        $deleted = $this->productRepository->delete($id);

        $this->rabbitMQService->publish('product.deleted', ['uuid' => $id]);

        return ApiResponse::success(null, 'Produto deletado com sucesso.');
    }
}