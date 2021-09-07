<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Transaction extends Model
{
    protected $table='transaction';
    protected $fillable=['user_id','order_id','order_number','wallet','payment_id','order_type'];

}