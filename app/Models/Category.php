<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use App\Traits\uuid\UseUuid;

class Category extends Model
{
    use UseUuid;

    protected $table = 'categories';

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'id',
        'name',
        'slug',
        'description',
        'parent_category_id'
    ];
}
