<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Cart extends Model
{
    protected $table='cart';
    protected $fillable=['user_id','item_id','addons_id','qty','price'];

    public function itemimage(){
        return $this->hasOne('App\ItemImages','item_id','item_id')->select('item_images.id','item_images.item_id',\DB::raw("CONCAT('".url('/storage/app/public/images/item/')."/', item_images.image) AS image"));
    }
}
