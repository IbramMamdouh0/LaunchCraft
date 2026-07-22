<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use MongoDB\Laravel\Eloquent\Model;

class Order extends Model
{
    use HasFactory;

    protected $connection = 'mongodb';
    protected $collection = 'orders';

    protected $fillable = [
        'website_id',
        'customer_name',
        'customer_phone',
        'items',
        'total_price',
        'status',
    ];

    protected $casts = [
        'items' => 'array',
        'total_price' => 'float',
    ];
}
