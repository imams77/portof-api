<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use App\Traits\uuid\UseUuid;

class OrderHistory extends Model
{
    use UseUuid;
    protected $table = 'order_history';

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'id',
        'user_id',
        'status',
        'product_detail',
        'status_code',
        'order_number',
        'order_id',
        'invoice_number',
        'total',
        'invoice_id',
        'ordered_at',
        'invoiced_at'
    ];

}
