<?php 

namespace App\Http\Middleware;

class CorsMiddleware {

  public function handle($request, \Closure $next)
  {
    $headers = [
      'Access-Control-Allow-Origin'      => '*',
      'Access-Control-Allow-Credentials' => 'true',
      'Access-Control-Max-Age'           => '86400',
      'Access-Control-Allow-Headers' => $request->header('Access-Control-Request-Headers'),
      'Access-Control-Allow-Methods' => $request->header('Access-Control-Request-Methods')
  ];

  if ($request->isMethod('OPTIONS'))
  {
      return response()->json('{"method":"OPTIONS"}', 200, $headers);
  }

  $response = $next($request);
  foreach($headers as $key => $value)
  {
      $response->header($key, $value);
  }

  return $response;

  }
}
