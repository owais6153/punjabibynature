<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class comboItem extends Model
{
   protected $table='combo_items';
   protected $fillable = ['name', 'group_id'];

   public function ComboGroup()
   {
   		return $this->belongsTo(ComboGroup::class, 'group_id');
   }
}