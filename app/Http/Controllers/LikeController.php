<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Like;

use Illuminate\Contracts\Auth\Factory as Auth;

class LikeController extends Controller
{
    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct(Auth $auth)
    {
      $this->auth = $auth;
    }

    public function update (Request $request, $product_id) {
      $user_id = $this->auth->user()->id;
    }
}
