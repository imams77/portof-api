<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use App\Traits\uuid\UseUuid;

class SalesOrder extends Model
{
    use UseUuid;

    protected $table = 'sales_order';

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'id',
        'full_name',
        'user_id',
        'email',
        'product_id',
        'product_name',
        'order_number',
        'phone_number',
        'price',
        'unique_price',
        'tax',
        'total',
        'expired_at',
        'download_url'
    ];
}
