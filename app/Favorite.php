<?php



namespace App;



use Illuminate\Database\Eloquent\Model;



class Favorite extends Model

{

    protected $table='favorite';

    protected $fillable=['user_id','item_id'];



    public function itemimage(){

        return $this->hasOne('App\ItemImages','item_id','id')->select('id','item_id',\DB::raw("CONCAT('".url('/storage/app/public/images/item/')."/', image) AS image"));

    }

    public function variation(){
        return $this->hasMany('App\Variation','item_id','id')->select('variation.id','variation.item_id','variation.variation','variation.product_price','variation.sale_price');
    }

}