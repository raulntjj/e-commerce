<?php

namespace App\Models;

use App\Models\Traits\HasUuid;
use App\Models\Traits\HasUserAction;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Order extends Model {
    use HasFactory, HasUuid, HasUserAction;

    protected $primaryKey = 'uuid';
    public $incrementing = false;
    protected $keyType = 'string';

    protected $fillable = [
        'uuid',
        'user_id',
        'total_amount',
        'status',
        'shipping_address_snapshot',
        'created_by',
        'updated_by',
        'deleted_by',
    ];

    protected $casts = [
        'shipping_address_snapshot' => 'array',
        'total_amount' => 'decimal:2',
    ];

    public function items(): HasMany
    {
        return $this->hasMany(OrderItem::class, 'order_uuid', 'uuid');
    }
}