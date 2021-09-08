<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class ComboGroup extends Model
{
   protected $table='combo_items';
   protected $fillable = ['name', 'group_id'];

   public function comboItem()
   {
   		return $this->hasMany(comboItem::class, 'group_id');
   }
}
