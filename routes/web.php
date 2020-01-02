<?php

/*
|--------------------------------------------------------------------------
| Application Routes
|--------------------------------------------------------------------------
|
| Here is where you can register all of the routes for an application.
| It is a breeze. Simly tell Lumen the URIs it should respond to
| and give it the Closure to call when that URI is requested.
|
*/
$router->group(['prefix' => '/api/v1', 'middleware' => 'cors'], function () use ($router) {
    $router->post('/register-admin', ['as' => 'auth.registerAdmin', 'uses' => 'AuthController@registerAdmin']);
    $router->post('/register-superadmin', ['as' => 'auth.registerSuperadmin','uses' => 'AuthController@registerSuperAdmin']);
    
    $router->post('/login', ['as' => 'auth.login','uses' => 'AuthController@login']);
    $router->post('/register', ['as' => 'auth.register', 'uses' => 'AuthController@register']);
    $router->get('/products', ['as' => 'product.index', 'uses' => 'ProductController@index']);
    $router->put('/products/{product_id}/{status}', ['as' => 'product.updateStatus', 'uses' => 'ProductController@updateStatus']);
    $router->get('/products/{product_id}', ['as' => 'product.show', 'uses' => 'ProductController@show']);
    $router->get('/users/{user_id}', ['as' => 'user.show','uses' => 'UserController@show']);
    $router->get('/users', ['as' => 'user.index', 'uses' => 'UserController@index']);
    $router->get('/categories', ['as' => 'category.index', 'uses' => 'CategoryController@index']);
    $router->get('/categories/{category_id}', ['as' => 'category.show', 'uses' => 'CategoryController@show']);

    $router->post('/sales-order', ['as' => 'so.create', 'uses' => 'OrderController@createSalesOrder']);
    $router->get('/sales-order/{order_number}', ['as' => 'so.detail', 'uses' => 'OrderController@showSalesOrder']);

    $router->post('/payment-confirmation', ['as' => 'payment.confirmation', 'uses' => 'OrderController@paymentConfirmation']);

    $router->get('/order-history/{order_id}', ['as' => 'order.history', 'uses' => 'OrderController@history']);

    
    $router->group(['middleware' => 'auth:api'], function () use ($router) {
        $router->get('/me', ['as' => 'auth.show', 'uses' => 'AuthController@show']);
        $router->get('/me/products', ['as' => 'user.products', 'uses' => 'UserController@products']);
        $router->put('/me/like', ['as' => 'user.products', 'uses' => 'UserController@products']);
        $router->get('/me/products', ['as' => 'user.products', 'uses' => 'UserController@products']);
        $router->get('/me/order-history', ['as' => 'order.history', 'uses' => 'UserController@orderHistory']);

        $router->put('/users/{user_id}', ['as' => 'user.update', 'uses' => 'UserController@update']);
        
        $router->put('/like/{product_id}', ['as' => 'like.storeLike', 'uses' => 'LikeController@update']);
        // $router->get('/user/likes');
        
        $router->group(['middleware' => 'role:admin'], function () use ($router) {
            $router->put('/products/{product_id}/accept', ['as' => 'product.accept', 'uses' => 'ProductController@accept']);
            $router->put('/products/{product_id}/reject', ['as' => 'product.reject', 'uses' => 'ProductController@reject']);
            // Categories
            $router->post('/categories', ['as' => 'category.store', 'uses' => 'CategoryController@store']);
            $router->put('/categories/{category_id}', ['as' => 'category.update', 'uses' => 'CategoryController@update']);
            $router->delete('/categories/{category_id}', ['as' => 'category.destroy', 'uses' => 'CategoryController@destroy']);

        });

        $router->group(['middleware' => 'role:creator'], function () use ($router) {
            $router->get('/me/products', ['as' => 'user.products', 'uses' => 'UserController@products']);
            $router->get('/me/products', ['as' => 'user.products', 'uses' => 'UserController@products']);
            
            $router->post('/upload/image', ['as' => 'upload.image', 'uses' => 'UploadController@image']);
            $router->post('/upload/file', ['as' => 'upload.file', 'uses' => 'UploadController@file']);
            $router->post('/products', ['as' => 'product.store', 'uses' => 'ProductController@store']);
            $router->put('/products/{product_id}', ['as' => 'product.update', 'uses' => 'ProductController@update']);
            $router->delete('/products/{product_id}', ['as' => 'product.destroy', 'uses' => 'ProductController@destroy']);
        });

    });
});
// $router->get('/key', function() {
//     $characters = '0123456789abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ';
//     $charactersLength = strlen($characters);
//     $randomString = '';
//     for ($i = 0; $i < 33; $i++) {
//         $randomString .= $characters[rand(0, $charactersLength - 1)];
//     }
//     return $randomString;
// });