<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class AddonGroups extends Model
{
    protected $table='addon_groups';

    protected $fillable=['name', 'price'];

    public function addons()
    {
    	return $this->hasMany(Addons::class,'group_id');
    }
}
