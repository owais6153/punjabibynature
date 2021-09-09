<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class ComboGroup extends Model
{
   protected $table='combo_group';
   protected $fillable = ['name', 'price'];

   public function ComboItem()
   {
   		return $this->hasMany(ComboItem::class, 'group_id');
   }
}

