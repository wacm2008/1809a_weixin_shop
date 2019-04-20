<?php

namespace App\Http\Controllers;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Session;
use Illuminate\Http\Request;
use App\GoodsModel;
use App\CarModel;
class CarController extends Controller
{
    //购物车展示
    public function index(){
        $carlist = CarModel::where(['u_id'=>Auth::id(),'session_id'=>Session::getId()])->get()->toArray();
        if($carlist){
            $total_price = 0;
            foreach($carlist as $k=>$v){
                $goodsInfo = GoodsModel::where(['goods_id'=>$v['goods_id']])->first()->toArray();
                $total_price += $v['goods_price']*$v['buy_number'];
                $goodslist[] = $goodsInfo;
            }
            $data = [
                'carlist'=>$carlist,
                'goodslist' => $goodslist,
                'total'     => $total_price
            ];
            return view('car/index',$data);
        }else{
            header('Refresh:3;url=/');
            die("购物车为空,跳转至首页");
        }
    }
    //购物车添加
    public function add($goods_id=0){
        if(empty($goods_id)){
            header('Refresh:3;url=/carlist');
            die("请选择商品，3秒后自动跳转至购物车");
        }
        //判断商品是否有效
        $goods = GoodsModel::where(['goods_id'=>$goods_id])->first();
        if($goods){
            if($goods->is_delete==1){
                header('Refresh:3;url=/');
                echo "商品已被删除,3秒后跳转至首页";
                die;
            }
            //添加购物车
            $carInfo=[
                'goods_id'  => $goods_id,
                'goods_name'    => $goods->goods_name,
                'goods_price'    => $goods->goods_price,
                'u_id'       => Auth::id(),
                'add_time'  => time(),
                'session_id' => Session::getId()
            ];
            $car=CarModel::insertGetId($carInfo);
            if($car){
                header('Refresh:3;url=/carlist');
                die("添加购物车成功，自动跳转至购物车");
            }else{
                header('Refresh:3;url=/');
                die("添加购物车失败");
            }
        }else{
            echo "<script>alert('无此商品！')</script>";
        }
    }
}
