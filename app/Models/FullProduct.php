<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class FullProduct extends Model
{
    protected $table = 'products';
    
    protected $fillable = [
        'name',
        'user_id', 
        'likes',
        'download_url',
        'thumbnail_url',
        'description',
        'price',
        'status',
        'category_id',
        'tags',
        'slug'
    ];
}
