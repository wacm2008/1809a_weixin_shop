<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class QcodeModel extends Model
{
    protected $table='tmp_wx_users';
    public $timestamps=false;
    protected $primaryKey='wid';
}