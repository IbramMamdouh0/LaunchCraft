<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use MongoDB\Laravel\Eloquent\Model;

class MenuItem extends Model
{
    use HasFactory;

    protected $connection = 'mongodb';
    protected $collection = 'menu_items';

    protected $fillable = [
        'website_id',
        'category_id',
        'title',
        'description',
        'price',
        'image',
        'is_available',
    ];

    protected $casts = [
        'price' => 'float',
        'is_available' => 'boolean',
    ];
}
