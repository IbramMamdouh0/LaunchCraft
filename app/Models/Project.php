<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use MongoDB\Laravel\Eloquent\Model;

class Project extends Model
{
    use HasFactory;

    protected $connection = 'mongodb';
    protected $collection = 'projects';

    protected $fillable = [
        'website_id',
        'title',
        'description',
        'images',
        'project_url',
        'category',
    ];

    protected $casts = [
        'images' => 'array',
    ];
}
