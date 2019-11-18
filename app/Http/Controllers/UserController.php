<?php

namespace App\Http\Controllers;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Tymon\JWTAuth\JWTAuth;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Str;
use App\Models\User;
use Illuminate\Support\Facades\DB;

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
      $data = $request->all('full_name', 'phone_number', 'account_number', 'account_name', 'is_creator');
      $validator = $this->validate($request, [
        'full_name'       => 'required'
      ]); 
      try {
        $current = User::findOrFail($user_id);
        $response = User::where('id', $user_id)->update([
          'full_name'       => !is_null($data['full_name']) ? $data['full_name'] : $current->full_name,
          'phone_number'    => !is_null($data['phone_number']) ? $data['phone_number'] : $current->phone_number,
          'account_number'  => !is_null($data['account_number']) ? $data['account_number'] : $current->account_number,
          'account_name'   => !is_null($data['account_name']) ? $data['account_name'] : $current->account_name,
          'is_creator'    => !is_null($data['is_creator']) ? $data['is_creator'] : $current->is_creator
        ]);
        return response()->json([
          "success"   => true,
          "data"      => User::findOrFail($user_id),
          "message"   => 'Data updated Successfully'
        ], 201);
    
      } catch (\Exception $e) {
          //return error message
          return response()->json(['message' => 'User Registration Failed!'], 409);
      }
    }

    public function products (Request $request) {
      $products = DB::table('products')->where('user_id', $this->jwt->user()->id);
      dd($products->get());
    }
}
