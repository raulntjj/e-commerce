<?php

namespace App\Repositories\Eloquent;

use App\Models\User;
use App\Repositories\Contracts\UserRepositoryInterface;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\ValidationException;

class UserRepository implements UserRepositoryInterface {
    protected $model;

    public function __construct(User $model) {
        $this->model = $model;
    }

    public function all(): Collection {
        return $this->model->all();
    }

    public function find(string $id): ?User {
        return $this->model->find($id);
    }

    public function findByEmail(string $email): ?User {
        return $this->model->where('email', $email)->first();
    }

    public function create(array $data): User {
        $data['password'] = Hash::make($data['password']);
        return $this->model->create($data);
    }

    public function update(string $id, array $data): ?User
    {
        $user = $this->find($id);
        if ($user) {
            if (isset($data['email'])) {
                $emailExists = $this->model->where('email', $data['email'])
                                           ->where('uuid', '!=', $id)
                                           ->exists();
                if ($emailExists) {
                    throw ValidationException::withMessages([
                        'email' => ['O e-mail informado já está em uso por outro usuário.'],
                    ]);
                }
            }
            
            if (!empty($data['password'])) {
                $data['password'] = Hash::make($data['password']);
            }

            $user->update($data);
            return $user;
        }
        return null;
    }

    public function delete(string $id): bool {
        $user = $this->find($id);
        if ($user) {
            return $user->delete();
        }
        return false;
    }
}