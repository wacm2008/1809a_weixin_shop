<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Str;
use GuzzleHttp\Client;
use GuzzleHttp\Psr7\Uri;
use Illuminate\Support\Facades\Storage;
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
        $mediaId=request()->serverId;
        
        $token=getAccessToken();
        $url='https://api.weixin.qq.com/cgi-bin/media/get?access_token='.$token.'&media_id='.$mediaId;
        //使用guzzle
        $clinet = new Client();
        $response=$clinet->get(new Uri($url));
        //获取响应头信息
        $headers=$response->getHeaders();
        //获取文件名
        $file_info=$headers['Content-disposition'][0];
        $file_name=rtrim(substr($file_info,-20),'""');
        $new_file_name='weixin/foto/'.substr(md5(time().mt_rand()),10,8).'_'.$file_name;
        //保存文件
        $foto=Storage::put($new_file_name,$response->getBody());
    }
}
