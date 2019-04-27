<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class WxmaterialModel extends Model
{
    protected $table='wxmaterial';
    public $timestamps=false;
    protected $primaryKey='mid';
}
