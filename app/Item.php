<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Item extends Model
{
    protected $table='item';
    protected $fillable=['cat_id','item_name','item_description','item_price','delivery_time', 'available_ing_option', 'is_default_combo', 'combo_group_id', 'item_type'];

    public function category(){
        return $this->hasOne('App\Category','id','cat_id');
    }
    public function variation(){
        return $this->hasMany('App\Variation','item_id','id')->select('variation.id','variation.item_id','variation.variation','variation.product_price','variation.sale_price');
    }

    public function itemimage(){
        return $this->hasOne('App\ItemImages','item_id','id')->select('item_images.id','image as image_name','item_images.item_id',\DB::raw("CONCAT('".url('/storage/app/public/images/item/')."/', item_images.image) AS image"));
    }

    public function itemimagedetails(){
        return $this->hasMany('App\ItemImages','item_id','id')->select('item_id','image as image_name',\DB::raw("CONCAT('".url('/storage/app/public/images/item/')."/', image) AS itemimage"));
    }

    public function ingredients(){
        return $this->hasMany('App\Ingredients','item_id','id')->select('item_id',\DB::raw("CONCAT('".url('/storage/app/public/images/ingredients/')."/', image) AS ingredients_image"));
    }

    public function addons(){
        return $this->hasMany('App\Addons','item_id','id')->select('id','name','price','item_id')->where('is_available','=','1');
    }
}