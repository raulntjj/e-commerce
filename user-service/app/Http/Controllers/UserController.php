<?php

namespace App\Http\Controllers;

use App\Repositories\Contracts\UserRepositoryInterface;
use App\Services\RabbitMQService;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use App\Http\Responses\ApiResponse;

class UserController extends Controller {
    protected RabbitMQService $rabbitMQService;
    private UserRepositoryInterface $userRepository;

    public function __construct(
        RabbitMQService $rabbitMQService, 
        UserRepositoryInterface $userRepository
    ) {
        $this->rabbitMQService = $rabbitMQService;
        $this->userRepository = $userRepository;
    }

    public function getAll(): JsonResponse {
        $users = $this->userRepository->all();
        return ApiResponse::success($users, 'Usuários listados com sucesso.');
    }

    public function get($id): JsonResponse {
        $user = $this->userRepository->find($id);

        if (!$user) {
            return ApiResponse::error('Usuário não encontrado', 404);
        }

        return ApiResponse::success($user);
    }

    public function create(Request $request): JsonResponse {
        $this->validate($request, [
            'name' => 'required|string',
            'email' => 'required|email|unique:users',
            'password' => 'required|string|min:6',
        ]);
        $user = $this->userRepository->create($request->all());
        
        $this->rabbitMQService->publish('user.created', $user->toArray());
        
        return ApiResponse::success($user, 'Usuário criado com sucesso.', 201);
    }

    public function update(Request $request, $id): JsonResponse {
        $user = $this->userRepository->find($id);
        if (!$user) {
            return ApiResponse::error('Usuário não encontrado', 404);
        }

        $this->validate($request, [
            'name' => 'sometimes|string',
            'email' => 'sometimes|email|unique:users,email,'.$id.',uuid',
            'password' => 'sometimes|string|min:6',
        ]);

        $updatedUser = $this->userRepository->update($id, $request->all());

        $this->rabbitMQService->publish('user.updated', $updatedUser->toArray());

        return ApiResponse::success($updatedUser, 'Usuário atualizado com sucesso.');
    }

    public function delete($id): JsonResponse {
        $user = $this->userRepository->find($id);
        if (!$user) {
            return ApiResponse::error('Usuário não encontrado', 404);
        }

        $this->userRepository->delete($id);

        $this->rabbitMQService->publish('user.deleted', ['uuid' => $id]);

        return ApiResponse::success(null, 'Usuário deletado com sucesso.');
    }
}