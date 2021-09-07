<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Time extends Model
{
    protected $table='time';
    protected $fillable=['day','open_time','close_time'];
}
