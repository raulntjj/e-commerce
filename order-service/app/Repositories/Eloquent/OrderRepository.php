<?php

namespace App\Repositories\Eloquent;

use App\Models\Order;
use App\Repositories\Contracts\OrderRepositoryInterface;
use Illuminate\Support\Collection;

class OrderRepository implements OrderRepositoryInterface {
    protected Order $model;

    public function __construct(Order $model) {
        $this->model = $model;
    }

    public function all(): Collection {
        return $this->model->all();
    }

    public function find(string $uuid): ?Order {
        return $this->model->where('uuid', $uuid)->first();
    }

    public function create(array $data): Order {
        return $this->model->create($data);
    }
    
    public function update(string $uuid, array $data): ?Order {
        $order = $this->find($uuid);
        if ($order) {
            $order->update($data);
            return $order;
        }
        return null;
    }
}
