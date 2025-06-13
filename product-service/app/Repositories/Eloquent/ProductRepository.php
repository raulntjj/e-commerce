<?php

namespace App\Repositories\Eloquent;

use App\Models\Product;
use App\Repositories\Contracts\ProductRepositoryInterface;
use Illuminate\Database\Eloquent\Collection;

class ProductRepository implements ProductRepositoryInterface {
    protected $model;

    public function __construct(Product $model) {
        $this->model = $model;
    }

    public function all(): Collection {
        return $this->model->all();
    }

    public function find(string $id): ?Product {
        return $this->model->find($id);
    }

    public function create(array $data): Product {
        return $this->model->create($data);
    }

    public function update(string $id, array $data): ?Product {
        $product = $this->find($id);
        if ($product) {
            $product->update($data);
            return $product;
        }
        return null;
    }

    public function delete(string $id): bool {
        $product = $this->find($id);
        if ($product) {
            return $product->delete();
        }
        return false;
    }
}