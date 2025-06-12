<?php

namespace App\Repositories\Eloquent;

use App\Models\User;
use App\Repositories\Contracts\UserRepositoryInterface;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Support\Facades\Hash;

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
}