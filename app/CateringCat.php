<?php

namespace App;

use Illuminate\Database\Eloquent\Model;



class CateringCat extends Model
{
    protected $table='catering_category';
  	protected $fillable = ['name', 'option_allowed', 'allowed_veg', 'allowed_nonveg'];
}
