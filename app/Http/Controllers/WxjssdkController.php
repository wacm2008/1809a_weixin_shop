<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Str;
class WxjssdkController extends Controller
{
    public function jssdk(){
//        $token=getAccessToken();
//        echo $token;
//        $ticket=getJsapiTicket();
//        echo $ticket;
        //计算签名
        $nonceStr = Str::random(10);
        $ticket = getJsapiTicket();
        $timestamp = time();
        $current_url = $_SERVER['REQUEST_SCHEME'] . '://' . $_SERVER['HTTP_HOST'] .$_SERVER['REQUEST_URI'];
        $string1 = "jsapi_ticket=$ticket&noncestr=$nonceStr&timestamp=$timestamp&url=$current_url";
        $sign = sha1($string1);
//        echo 'nonceStr: '.$nonceStr;echo '</br>';
//        echo 'ticket: '.$ticket;echo '</br>';
//        echo '$timestamp: '.$timestamp;echo '</br>';
//        echo '$current_url: '.$current_url;echo '</br>';
//        echo '$string1: '.$string1;echo '</br>';
//        echo '$sign: '.$sign;echo '</br>';die;
        $js_config=[
            'appId'=>env('WX_APPID'),
            'timestamp'=>$timestamp,
            'nonceStr'=>$nonceStr,
            'signature'=>$sign,
        ];
        $data=[
            'jsconfig'=>$js_config
        ];
        return view('weixin/jssdk',$data);
    }
    public function foto(){
        print_r($_GET);
    }
}
