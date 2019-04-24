<?php

namespace App\Http\Controllers\Crontab;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\OrderModel;
class CrontabController extends Controller
{
    public function delorder(){
        $data=OrderModel::all();
        foreach ($data as $k=>$v){
            if(time()-$v->add_time>1800&&$v->pay_time==0){
                OrderModel::where(['o_id'=>$v->o_id])->update(['is_delete'=>1]);
            }
        }
    }
}
