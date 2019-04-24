<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class WxuserModel extends Model
{
    protected $table='wxusers';
    public $timestamps=false;
    protected $primaryKey='wid';
}
