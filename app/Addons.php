<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Addons extends Model
{
    protected $table='addons';
    protected $fillable=['cat_id','item_id','name','price'];

    public function category(){
        return $this->hasOne('App\Category','id','cat_id');
    }

    public function item(){
        return $this->hasOne('App\Item','id','item_id');
    }

    public function addonsGroups()
    {
    	return $this->hasOne(AddonGroups::class,'group_id');
    }
}
