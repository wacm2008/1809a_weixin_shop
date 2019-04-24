<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class WxtextModel extends Model
{
    protected $table='wxtext';
    public $timestamps=false;
    protected $primaryKey='tid';
}
