<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Tymon\JWTAuth\JWTAuth;
use Illuminate\Support\Str;

use App\Models\User;

class AuthController extends Controller
{
    /**
     * Create a new controller instance.
     *
     * @return void
     */
    protected $jwt;

    public function __construct(JWTAuth $jwt)
    {
        $this->jwt = $jwt;
    }

    public function registerAdmin (Request $request) {
      $email = $request->input('email');
      $password = Hash::make($request->input('password'));
      $register = User::create([
        'email'     => $email,
        'password'  => $password,
        'user_type'  => 2
      ]);
    }

    public function registerSuperAdmin (Request $request) {
      $email = $request->input('email');
      $password = Hash::make($request->input('password'));

      $register = User::create([
        'email'     => $email,
        'password'  => $password,
        'user_type'  => 3
      ]);
    }

    public function login (Request $request) {

      $email = $request->input('email');
      $password = $request->input('password');

      $user = User::where('email', $email)->first();
      if (!is_null($user)) {
        if (Hash::check($password, $user->password)) {
          $token = $this->jwt->attempt($request->only('email', 'password'));
          // $request->session()->put('user_id', $user->id);
          return response()->json([
            'success'   => true,
            'message'   => 'Welcome!',
            'data'      => [
              'user'      => $user,
              'jwt'     => $this->respondWithToken($token)
            ]
            ], 201);
        } else {
          return response()->json([
            'success'   => false,
            'message'   => 'Wrong Password',
            'data'      => ''
            ], 400);
        }
      } else {
        return response()->json([
          'success'   => false,
          'message'   => 'Email Not Found',
          'data'      => ''
        ]);
      }
    }

    public function register (Request $request) {
      $email = $request->input('email');
      $password = Hash::make($request->input('password'));

      $register = User::create([
        'email'     => $email,
        'password'  => $password,
        'user_type'  => 0
      ]);
      if ($register) {
        return response()->json([
          'success'   => true,
          'message'   => 'Register Success!',
          'data'      => $register
        ], 201);
      } else {
        return response()->json([
          'success'   => false,
          'message'   => 'Register failed!',
          'data'      => ''
        ], 400);
      }
    }

    public function show (Request $request) {
      $user = $this->jwt->user();
      if (!is_null($user)) {
        $id = $user->toArray()['id'];
        $userDetail = User::where('id', $id)->first();
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
}
