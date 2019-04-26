<?php

use Illuminate\Routing\Router;

Admin::registerAuthRoutes();

Route::group([
    'prefix'        => config('admin.route.prefix'),
    'namespace'     => config('admin.route.namespace'),
    'middleware'    => config('admin.route.middleware'),
], function (Router $router) {

    $router->get('/', 'HomeController@index')->name('admin.home');
    //微信用户
    $router->get('/wxusers', 'WxController@index');
    //商品信息
    Route::resource('/goods', GoodsController::class);
    //订单信息
    Route::resource('/orders', OrderController::class);
    //发送图片
    Route::resource('/fotos', FotoController::class);
    //发送文字
    Route::resource('/texts', TextController::class);
    //发送语音
    Route::resource('/voices', VoiceController::class);
});
