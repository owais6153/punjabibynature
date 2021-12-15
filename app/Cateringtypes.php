<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
 
class Cateringtypes extends Model
{
   protected $table='catering_group';
   protected $fillable = ['name', 'price'];

   public function cateringaddon()
   {
        return $this->hasMany(Cateringaddon::class, 'group_id');
   }
}
