<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;

class Order extends Model
{
    protected $table = 'orders';
    protected $primaryKey = 'id';
    public $incrementing = false;
    protected $keyType = 'string';

    protected $fillable = [
        'external_id',
        'raw_data',
        'origin',
        'customer_id',
        'order_key',
        'currency',
        'billing_name',
        'billing_address',
        'shipping_name',
        'shipping_address',
        'items',
        'total',
        'discount_total',
        'shipping_total',
        'status',
        'last_error',
    ];

    protected $casts = [
        'raw_data' => 'array',
        'items' => 'array',
        'total' => 'float',
        'discount_total' => 'float',
        'shipping_total' => 'float',
    ];

    protected static function boot()
    {
        parent::boot();

        static::creating(function ($order) {
            $order->id = (string) Str::uuid();  
        });
    }
}