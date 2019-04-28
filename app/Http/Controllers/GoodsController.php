<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\GoodsModel;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Redis;
use Illuminate\Support\Str;
class GoodsController extends Controller
{
    //商品详情
    public function goodsdetail($goods_id){
        //echo $goods_id;
        if(!$goods_id){
            return $goods_id;
        }
        //$detailInfo=GoodsModel::where(['goods_id'=>$goods_id])->first();

        //dd($detailInfo);
        //浏览商品自增
//        $browse=$detailInfo['goods_browse']+1;
//        $goods_browse=GoodsModel::where(['goods_id'=>$goods_id])->update(['goods_browse'=>$browse]);
//        return view('goods/goodsdetail',compact('detailInfo','browse'));
        //redis浏览商品自增
        $browse_key=$goods_id;
        $browse=Redis::incr($browse_key);
        //有序集合 浏览排名
        $range='ss:goods_view';
        $rank=Redis::zAdd($range,$browse,$goods_id);
        return view('goods/goodsdetail',compact('detailInfo','browse'));
    }
    //商品浏览排名
    public function ranking(){
        $key='ss:goods_view';
        //正序
        //$list1=Redis::zRangeByScore($key,0,100,['withscores'=>true]);
        //print_r($list1);
        //倒序
        $list2=Redis::zRevRange($key,0,100,true);
        //print_r($list2);
        $llave=array_keys($list2);
        //print_r($llave);
        $parto=array_chunk($llave,3);
        //print_r($parto);
        $borra=array_shift($parto);
        //print_r($borra);
        $browse=[];
        foreach ($borra as $k=>$v){
           $browse[]=GoodsModel::where(['goods_id'=>$v])->first();
        }
        //var_dump($browse);exit;
        $data=GoodsModel::get();
        //dd($data);
        return view('goods/goodslist',['data'=>$data,'browse'=>$browse]);
    }
    //最新商品
    public function newgoods(){
        $goods=GoodsModel::orderBy('goods_id','desc')->take(1)->get();
        $jsconfig=$this->jssdk();
        $data=[
            'jsconfig'=>$jsconfig,
            'goods'=>$goods
        ];
        $server=[
            'server'=>$_SERVER['REQUEST_SCHEME'] . '://' . $_SERVER['SERVER_NAME'] .$_SERVER['REQUEST_URI']
        ];

        return view('goods/goods',$data,$server);
    }

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
        return $js_config;
    }
}
