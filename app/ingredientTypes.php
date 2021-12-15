<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class IngredientTypes extends Model
{
	protected $table='ingredient_types';
 
    protected $fillable=['name'];

    public function ingredients()
    {
        return $this->hasMany(Ingredients::class, 'type_id');
    }
}
