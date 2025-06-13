<?php

namespace App\Models;

use App\Models\Traits\HasUuid;
use App\Models\Traits\HasUserAction;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Product extends Model {
    use HasFactory, HasUuid, HasUserAction;

    protected $fillable = [
        'name', 'description', 'price', 'stock',
    ];
}