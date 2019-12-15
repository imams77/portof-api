<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class SalesOrder extends Model
{
    protected $table = 'sales_order';

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'full_name',
        'user_id',
        'email',
        'product_id',
        'product_name',
        'order_number',
        'phone_number',
        'price',
        'expired_at',
        'download_url'
    ];
}
