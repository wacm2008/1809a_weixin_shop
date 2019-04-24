<?php

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| contains the "web" middleware group. Now create something great!
|
*/

Route::get('/', function () {
    return view('welcome');
});

Auth::routes();

Route::get('/home', 'HomeController@index')->name('home');

//购物车展示
Route::get('/carlist','CarController@index');
//购物车添加
Route::get('/caradd/{goods_id?}', 'CarController@add');
//订单列表
Route::get('/order/list', 'OrderController@orderlist');
//生成订单
Route::get('/order/create', 'OrderController@create');
//查询订单支付状态
Route::get('/order/paystatus', 'OrderController@paystatus');
//微信支付
Route::get('/weixin/pay', 'WxpayController@pay');
//微信支付通知回调
Route::post('/weixin/notify', 'WxpayController@notify');
//支付成功
Route::get('/pay/success', 'WxpayController@paySuccess');
//商品详情
Route::get('/goodsdetail/{goods_id}', 'GoodsController@goodsdetail');
//商品浏览排名
Route::get('/goodsranking', 'GoodsController@ranking');
//微信jssdk
Route::get('/jssdk/test', 'WxjssdkController@jssdk');
//微信jssdk获取上传照片
Route::get('/jssdk/foto','WxjssdkController@foto');

//微信首次连接
Route::get('/weixin/valid','WxController@valid');
//接收微信服务推送
Route::post('/weixin/valid','WxController@valide');
Route::get('/weixin/accesstoken','WxController@getAccessToken');
//微信菜单测试
Route::get('/weixin/atoken','WxController@getaToken');
//微信菜单创建
Route::get('/weixin/createmenu','WxController@createMenu');
Route::get('/weixin/test','WxController@test');
//微信群发
Route::get('/weixin/send','WxController@send');
//最新商品
Route::get('/newgoods/{goods_id}', 'GoodsController@newgoods');