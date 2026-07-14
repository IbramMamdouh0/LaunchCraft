<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use MongoDB\Laravel\Eloquent\Model;

class Website extends Model
{
    use HasFactory;

    protected $connection = 'mongodb';
    protected $collection = 'websites';

    protected $fillable = [
        'user_id',
        'name',
        'business_type',
        'template',
        'theme',
        'status',
        'slug',
        'pages',
    ];

    protected $casts = [
        'theme' => 'array',
        'pages' => 'array',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function media()
    {
        return $this->hasMany(Media::class);
    }
}
