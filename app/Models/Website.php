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
        'domain',
        'business_type',
        'template',
        'theme',
        'sections',
        'status',
        'is_published',
        'slug',
        'pages',
    ];

    protected $casts = [
        'theme' => 'array',
        'sections' => 'array',
        'pages' => 'array',
        'is_published' => 'boolean',
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
