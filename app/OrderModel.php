<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;
class OrderModel extends Model
{
    protected $table='p_order';
    public $timestamps=false;
    protected $primaryKey='o_id';
    //生成订单编号
    public static function generateOrderSN($uid)
    {
        $order_sn = '1809a_'. date("ymdH").'_';
        $str = time() . $uid . rand(1111,9999) . Str::random(16);
        $order_sn .=  substr(md5($str),5,16);
        return $order_sn;
    }
}
