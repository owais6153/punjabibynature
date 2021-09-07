<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Payment extends Model
{
    protected $table='payment';
    protected $fillable=['user_id','payment_name','is_available','test_public_key','test_secret_key','live_public_key','live_secret_key','environment'];

}