<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\FullProduct;
use App\Models\User;
use App\Models\SalesOrder;
use App\Models\OrderHistory;
use Webpatser\Uuid\Uuid;

use App\Helpers;

use DateTime;
use DateInterval;

class OrderController extends Controller
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

    public function createSalesOrder (Request $request)
    {
        try {
            $validator = $this->validate($request, [
                'full_name'     => 'required',
                'phone_number'  => 'required',
                'email'         => 'required',
                'product_id'    => 'required'
            ]);
            $data = $request->all();

            $product = FullProduct::find($data['product_id']);
            $user = User::find($data['user_id']);

            $total_sales_order = SalesOrder::all();
            $sales_order_number = 'SO-'.Helpers::generateDigits(count($total_sales_order) + 1);

            $datetime = new DateTime();
            $expired_at = $datetime->add(new DateInterval("P2D"));

            // price
            $tax = 0;
            $unique_price = rand(11, 199);
            $total = $product['price'] + $tax + $unique_price;
            
            if ($user !== null && $product !== null) {
                $user = $user->toArray();
                $uuid = Uuid::generate(4)->string;
                $sales_order = [
                    'id'            => $uuid,
                    'full_name'     => $data['full_name'],
                    'user_id'       => $data['user_id'],
                    'email'         => $data['email'],
                    'product_id'    => $data['product_id'],
                    'product_name'  => $product['name'],
                    'order_number'  => $sales_order_number,
                    'phone_number'  => $data['phone_number'],
                    'price'         => $product['price'],
                    'unique_price'  => $unique_price,
                    'tax'           => $tax,
                    'total'         => $total,
                    'expired_at'    => $expired_at,
                    'download_url'  => $product['download_url']
                ];
                $response = SalesOrder::create($sales_order);
                try {
                    $order_uuid = Uuid::generate(4)->string;
                    $ordered_at = new DateTime($response->created_at);
                    OrderHistory::create([
                        'id'                => $order_uuid,
                        'user_id'           => $data['user_id'],
                        'product_detail'    => json_encode($product),
                        'status'            => 'Awaiting Payment',
                        'order_number'      => $response->order_number,
                        'order_id'          => $response->id,
                        'total'             => $total,
                        'status_code'       => 0,
                        'ordered_at'        => $ordered_at
                    ]);
                    return Helpers::generateResponse("Successfully create sales order.", $response)->success;
                } catch (\Exception $e) {
                    return Helpers::generateResponse("Failed create sales order.")->fail;
                }
            }
            return Helpers::generateResponse("Product and/or user not found.")->fail;
        } catch (\Exception $e) {
            $errors = Helpers::generateValidationErrors($e->errors());
            return Helpers::generateResponse("Product and/or user not found.", $errors)->fail;
        }
        
    }

    public function showSalesOrder (Request $request, $order_number) {
        $sales_order = SalesOrder::where('order_number', '=', $order_number)->get();
        if (count($sales_order->toArray()) > 0) {
            return Helpers::generateResponse("Sales Order Found.", $sales_order)->success;
        } else {
            return Helpers::generateResponse("Sales Order Not Found.")->fail;
        }
    }

    public function history (Request $request, $order_id) {
        $order_history = orderHistory::where('order_id', '=', $order_id)->first();
        if ($order_history !== null) {
            return Helpers::generateResponse("Found order.", $order_history)->success;
        } else {
            return Helpers::generateResponse("Order Not Found.")->success;
        }
    }

    public function paymentConfirmation (Request $request) {
        try {
            $validator = $this->validate($request, [
                'full_name'     => 'required',
                'bank_account'  => 'required',
                'account_name'  => 'required',
                'email'         => 'required',
                'bank_name'     => 'required',
                'payment_proof' => 'required',
                'paid_at'       => 'required|date',
                'order_number'  => 'required'
            ]);
            $data = $request->all();
            $order_history = OrderHistory::where('order_number', '=', $request->get('order_number'))->first();
            if ($order_history !== null) {
                try {
                    $order_history->update([
                        'status'        => 'Awaiting Confirmation',
                        'status_code'   => 1
                    ]);
                    return Helpers::generateResponse("Your payment is being processed. Please wait for our confirmation.")->success;
                } catch (\Exception $e) {
                    return Helpers::generateResponse("Failed to create payment confirmation.")->fail;
                }
            }
        } catch (\Exception $e) {
            $errors = Helpers::generateValidationErrors($e->errors());
            return Helpers::generateResponse("Please complete the form.", $errors)->fail;
        }
    }
}
