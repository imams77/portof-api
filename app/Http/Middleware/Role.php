<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Contracts\Auth\Factory as Auth;

class Role
{
    /**
     * The authentication guard factory instance.
     *
     * @var \Illuminate\Contracts\Auth\Factory
     */
    protected $auth;

    public function __construct(Auth $auth)
    {
        $this->auth = $auth;
    }

    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure  $next
     * @return mixed
     */
    
    public function handle($request, Closure $next, $guard = null)
    {
      $user = $this->auth->user();
      $has_access = false;
      switch ($guard) {
        case 'superadmin':
          $has_access = $user->user_type >= 3;
          break;
        case 'admin':
          $has_access = $user->user_type >= 2;
          break;
        case 'creator':
          $has_access = $user->user_type >= 1;
          break;
        default:
          $has_access = false;
          break;
      }
      if ($has_access) {
        return $next($request);
      }
      return response($guard.' only.', 401);
    }
}
