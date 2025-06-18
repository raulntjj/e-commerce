<?php

namespace App\Models;

use App\Models\Traits\HasUuid;
use App\Models\Traits\HasUserAction;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Order extends Model {
    use HasFactory, HasUuid, HasUserAction;

    protected $primaryKey = 'uuid';
    public $incrementing = false;
    protected $keyType = 'string';

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
        'total_amount' => 'decimal:2',
    ];
}