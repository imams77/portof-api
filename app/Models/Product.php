<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Product extends Model
{
    protected $table = 'products';

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
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

    protected $hidden = [
        'download_url'
    ];
}
