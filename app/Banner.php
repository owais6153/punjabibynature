<?php



namespace App;



use Illuminate\Database\Eloquent\Model;



class Banner extends Model

{

    protected $table='banner';

    protected $fillable=['image'];



    public function item(){
        return $this->hasOne('App\Item','id','item_id');
    }

    public function category(){
        return $this->hasOne('App\Category','id','cat_id');
    }

}

