<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\FullProduct;
use App\Models\User;
use App\Models\SalesOrder;
use App\Models\PaymentHistory;

use App\Helpers;

use DateTime;
use DateInterval;

class SalesOrderController extends Controller
{
    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        //
    }

    public function store (Request $request)
    {
        $data = $request->all();
        $product = FullProduct::find($data['product_id']);
        $user = User::find($data['user_id']);
        $total_sales_order = SalesOrder::all();
        $sales_order_number = 'SO-'.Helpers::generateDigits(count($total_sales_order) + 1);
        $datetime = new DateTime();
        $expired_at = $datetime->add(new DateInterval("P2D"));
        if ($user !== null && $product !== null) {
            $user = $user->toArray();
            $sales_order = [
                'full_name'     => $data['full_name'],
                'user_id'       => $data['user_id'],
                'email'         => $user['email'],
                'product_id'    => $data['product_id'],
                'product_name'  => $product['name'],
                'order_number'  => $sales_order_number,
                'phone_number'  => $user['phone_number'] || 0,
                'price'         => $product['price'],
                'expired_at'    => $expired_at,
                'download_url'  => $product['download_url']
            ];
            try {
                SalesOrder::create($sales_order);
                PaymentHistory::create([
                    'user_id'           => $data['user_id'],
                    'product_detail'    => $product,
                    'status'            => 'waiting for payment',
                    'status_code'       => 0
                ]);
                return Helpers::generateResponse("Successfully create sales order.", $sales_order)->success;
            } catch (\Exception $e) {
                return Helpers::generateResponse("Failed create sales order.")->fail;
            }
        }
        return Helpers::generateResponse("Product and/or user not found.")->fail;
    }

    public function show (Request $request, $order_number) {
        $sales_order = SalesOrder::where('order_number', '=', $order_number)->get();
        if (count($sales_order->toArray()) > 0) {
            return Helpers::generateResponse("Sales Order Found.", $sales_order)->success;
        } else {
            return Helpers::generateResponse("Sales Order Not Found.")->fail;
        }
    }
}
