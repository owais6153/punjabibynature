<?php



namespace App;



use Illuminate\Database\Eloquent\Model;



class Ingredients extends Model

{

    protected $table='ingredients';

    protected $fillable=['item_id','image', 'type_id'];


    public function IngredientTypes()
    {
        return $this->belongsTo(IngredientTypes::class, 'type_id');
    }
}

