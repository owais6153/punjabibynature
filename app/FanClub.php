<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class FanClub extends Model
{
   protected $table='club';
   protected $fillable = ['reviewer_name', 'reviewer_image', 'reviewer_rating', 'reviewer_review', 'reviewer_link']; 

   public function fangroup()
   {
         return $this->belongsTo(FanClub::class, 'group_id');
   }   
}
