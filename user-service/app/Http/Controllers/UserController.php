<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Services\RabbitMQService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Http\JsonResponse;

class UserController extends Controller {
    protected RabbitMQService $rabbitMQService;

    public function __construct(RabbitMQService $rabbitMQService) {
        $this->rabbitMQService = $rabbitMQService;
    }

    public function create(Request $request): JsonResponse {
        $this->validate($request, [
            'name' => 'required|string',
            'email' => 'required|email|unique:users',
            'password' => 'required|string|min:6',
        ]);

        $user = User::create([
            'name' => $request->input('name'),
            'email' => $request->input('email'),
            'password' => Hash::make($request->input('password')),
        ]);
        
        $this->rabbitMQService->publish('user.created', $user->toArray());
        
        return response()->json($user, 201);
    }

    public function get($id): JsonResponse {
        $user = User::find($id);

        if (!$user) {
            return response()->json(['error' => 'User not found'], 404);
        }

        return response()->json($user);
    }

    public function getAllUsers(): JsonResponse {
        $users = User::all();

        return response()->json($users);

        // if ($users->isEmpty()) {
        //     return response()->json(['message' => 'No users found'], 404);
        // }
        // return response()->json($users);
    }
}