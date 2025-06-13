<?php

namespace App\Repositories\Contracts;

use App\Models\Product;
use Illuminate\Database\Eloquent\Collection;

interface ProductRepositoryInterface {
    public function all(): Collection;
    public function find(string $id): ?Product;
    public function create(array $data): Product;
    public function update(string $id, array $data): ?Product;
    public function delete(string $id): bool;
}