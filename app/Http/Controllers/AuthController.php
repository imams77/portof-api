<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Tymon\JWTAuth\JWTAuth;
use Illuminate\Support\Str;
use Webpatser\Uuid\Uuid;

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
      // dd($this);
      $uuid = Uuid::generate(4)->string;
      $email = $request->input('email');
      $password = Hash::make($request->input('password'));
      $user = [
        'id'        => $uuid,
        'email'     => $email,
        'password'  => $password,
        'user_type'  => 2,
        'is_creator'  => true
      ];
      $register = User::create($user);
    }

    public function registerSuperAdmin (Request $request) {
      $email = $request->input('email');
      $password = Hash::make($request->input('password'));

      $register = User::create([

        'email'     => $email,
        'password'  => $password,
        'user_type'  => 3,
        'is_creator'  => true
      ]);
    }

    public function login (Request $request) {

      $email = $request->input('email');
      $password = $request->input('password');
      $user = User::where('email', $email)->first();
      if (!is_null($user)) {
        if (Hash::check($password, $user->password)) {
          // $token = $this->jwt->attempt($request->only('email', 'password'));
          $token = $this->jwt->attempt($request->only('email', 'password'));
          // $request->session()->put('user_id', $user->id);
          return response()->json([
            'success'   => true,
            'message'   => 'Welcome!',
            'data'      => [
              'user'      => $user->toArray(),
              'jwt'     => $this->respondWithToken($token)
            ]
            ], 201);
        } else {
          return response()->json([
            'success'   => false,
            'message'   => 'Wrong Password',
            'data'      => ''
            ], 201);
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

      $validator = $this->validate($request, [
        'email'           => 'required',
        'password'       => 'required|min:6'
      ]); 
      
      try {
        $user = User::where('email', $email)->first();
        if (is_null($user)) {
          $uuid = Uuid::generate(4)->string;
          $register = User::create([
            'id'      => $uuid,
            'email'     => $email,
            'password'  => $password,
            'user_type'  => 0,
            'is_creator'  => false
          ]);
          try {
            return response()->json([
              'success'   => true,
              'message'   => 'Register Success!',
              'data'      => $register
            ], 201);
          } catch (\Exception $e) {
            return response()->json([
              'success'   => false,
              'message'   => $e,
              'data'      => ''
            ], 201);
          }
        } else {
          return response()->json([
            'success'   => false,
            'message'   => 'Email already registered.',
            'data'      => ''
          ], 201);
        }
      } catch(\Exception $e) {
        return response()->json(['message' => 'User Registration Failed!'], 409);
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
