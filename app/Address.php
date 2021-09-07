<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Address extends Model
{
    protected $table='address';
    protected $fillable=['user_id','full_name','address_type','address','lat','lang','landmark','building','mobile','pincode'];
}
