<?php

namespace App\Models;

use App\Models\Traits\HasUuid;
use App\Models\Traits\HasUserAction;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use MongoDB\Laravel\Eloquent\Model;

class Order extends Model {
    use HasFactory, HasUuid, HasUserAction;

    protected $connection = 'mongodb';

    protected $collection = 'orders';

    protected $primaryKey = '_id';

    protected $fillable = [
        'uuid',
        'user_id', 
        'products', 
        'total_amount', 
        'status', 
        'shipping_address',
        'created_by',
        'updated_by',
        'deleted_by',
    ];
    
    protected $casts = [
        'products' => 'array',
        'shipping_address' => 'array',
        'total_amount' => 'integer',
    ];
}
