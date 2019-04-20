<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\Facades\Auth;
use App\OrderModel;
use App\CarModel;
use App\OrderDetailModel;
class OrderController extends Controller
{
    //订单生成
    public function create(){
        $carlist = CarModel::where(['u_id'=>Auth::id(),'session_id'=>Session::getId()])->get()->toArray();
        $order_amount = 0;
        foreach($carlist as $k=>$v){
            $order_amount += $v['goods_price']*$v['buy_number'];
        }
        $order_info = [
            'u_id'              => Auth::id(),
            'order_sn'          => OrderModel::generateOrderSN(Auth::id()),//订单编号
            'order_amount'      => $order_amount,
            'add_time'          => time()
        ];
        $oid = OrderModel::insertGetId($order_info);
        //订单详情
        foreach($carlist as $k=>$v){
            $detail = [
                'o_id'           => $oid,
                'goods_id'      => $v['goods_id'],
                'goods_name'    => $v['goods_name'],
                'goods_price'   => $v['goods_price'],
                'buy_number'    =>$v['buy_number'],
                'u_id'           => Auth::id()
            ];
            //写入订单详情表
            OrderDetailModel::insertGetId($detail);
        }
        header('Refresh:3;url=/order/list');
        echo "生成订单成功";
    }
    //订单列表
    public function orderlist(){
        $list = OrderModel::where(['u_id'=>Auth::id()])->orderBy("o_id","desc")->get()->toArray();
        $data = [
            'list'  => $list
        ];
        return view('order.index',$data);
    }
    //订单状态
    public function paystatus()
    {
        $oid = intval($_GET['o_id']);
        $info = OrderModel::where(['o_id'=>$oid])->first();
        $res = [];
        if($info){
            if($info->pay_time>0){
                $res = [
                    'status'    => 0,
                    'msg'       => 'ok'
                ];
            }
        }else{
            die("订单不存在");
        }
        die(json_encode($res));
    }
}
