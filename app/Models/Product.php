<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use App\Traits\uuid\UseUuid;

class Product extends Model
{
    use UseUuid;
    protected $table = 'products';

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'id',
        'name',
        'user_id', 
        'likes',
        'download_url',
        'thumbnail_url',
        'description',
        'price',
        'status',
        'download_times',
        'category_id',
        'tags',
        'slug'
    ];

    protected $hidden = [
        'download_url'
    ];
}
