<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use MongoDB\Laravel\Eloquent\Model;

class Media extends Model
{
    use HasFactory;

    protected $connection = 'mongodb';
    protected $collection = 'media';

    protected $fillable = [
        'website_id',
        'filename',
        'path',
        'size',
    ];

    public function website()
    {
        return $this->belongsTo(Website::class);
    }
}
