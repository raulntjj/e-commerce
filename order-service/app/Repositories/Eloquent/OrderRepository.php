<?php

namespace App\Repositories\Eloquent;

use App\Models\Order;
use App\Repositories\Contracts\OrderRepositoryInterface;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;

class OrderRepository implements OrderRepositoryInterface {
    protected Order $model;

    public function __construct(Order $model) {
        $this->model = $model;
    }

    public function all(): Collection {
        return $this->model->with('items')->get();
    }

    public function find(string $uuid): ?Order {
        return $this->model->with('items')->where('uuid', $uuid)->first();
    }

    public function create(array $data): Order {
        return DB::transaction(function () use ($data) {
            $order = $this->model->create([
                'user_id' => $data['user_id'],
                'total_amount' => $data['total_amount'],
                'status' => $data['status'],
                'shipping_address_snapshot' => $data['shipping_address'],
            ]);

            foreach ($data['items'] as $item) {
                $order->items()->create([
                    'product_uuid' => $item['product_id'],
                    'product_name' => $item['name'],
                    'quantity' => $item['quantity'],
                    'price' => $item['price'],
                    'total' => $item['quantity'] * $item['price'],
                ]);
            }
            
            return $order->load('items');
        });
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