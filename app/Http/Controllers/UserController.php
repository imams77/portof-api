<?php

namespace App\Http\Controllers;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Tymon\JWTAuth\JWTAuth;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Str;
use App\Models\User;
use Illuminate\Support\Facades\DB;

use App\Helpers;

class UserController extends Controller
{
    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct(JWTAuth $jwt)
    {
        $this->jwt = $jwt;
    }

    public function show (Request $request, $user_id = null) {
      if (!is_null($user_id)) {
        $userDetail = User::where('id', $user_id)->first();
        if ($userDetail) {
          return response()->json([
            'success'   => true,
            'data'      => $userDetail,
            'message'   => 'success'
          ], 201);
        } else {
          return response()->json([
            'success'   => true,
            'data'      => '',
            'message'   => 'User Not Found'
          ], 201);
        }
      }
      return response()->json([
        'success'   => false,
        'message'   => 'User not found',
        'data'      => ''
      ], 400);
    }

    public function update(Request $request, $user_id) {
      // dd('123');
      $data = $request->all('full_name', 'phone_number', 'account_number', 'account_name', 'is_creator', 'user_type');
      // $validator = $this->validate($request, [
      //   'full_name'       => 'required'
      // ]);
      try {
        $current = User::findOrFail($user_id);
        $response = User::where('id', $user_id)->update([
          'full_name'       => !is_null($data['full_name']) ? $data['full_name'] : $current->full_name,
          'phone_number'    => !is_null($data['phone_number']) ? $data['phone_number'] : $current->phone_number,
          'account_number'  => !is_null($data['account_number']) ? $data['account_number'] : $current->account_number,
          'account_name'   => !is_null($data['account_name']) ? $data['account_name'] : $current->account_name,
          'is_creator'    => !is_null($data['is_creator']) ? $data['is_creator'] : $current->is_creator,
          'user_type'    => !is_null($data['user_type']) ? $data['user_type'] : $current->user_type
          ]);
        return response()->json([
          "success"   => true,
          "data"      => User::findOrFail($user_id),
          "message"   => 'Data updated Successfully'
        ], 201);
    
      } catch (\Exception $e) {
          //return error message
          return response()->json(['message' => 'Update User Failed!'], 400);
      }
    }

    public function products (Request $request) {
      $products = DB::table('products')->where('user_id', $this->jwt->user()->id);
      return Helpers::generateResponse("Success.", $products->get())->success;
    }

    public function orderHistory (Request $request) {
      $orderHistory = DB::table('order_history')->where('user_id', $this->jwt->user()->id);
      if ($request->get('status') !== null) {
        $orderHistory = $orderHistory->where('status', '=', $request->get('status'));
      }
      if ($request->get('status-code') !== null) {
        $orderHistory = $orderHistory->where('status_code', '=', $request->get('status-code'));
      }
      return Helpers::generateResponse("Success.", $orderHistory->get())->success;
    }
}
