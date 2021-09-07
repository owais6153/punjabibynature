<?php

namespace App\Http\Controllers\admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\IngredientTypes;
use Validator;

class IngredientTypeController extends Controller
{
   public function index()
    {
        
        $getingredientTypes = IngredientTypes::all();
        
        return view('ingredientType',compact('getingredientTypes'));
    }
    public function store(Request $request){

        $validation = Validator::make($request->all(),[
            'ingredients_name' => 'required'            
        ]);
        $error_array = array();
        $success_output = '';
        if ($validation->fails())
        {
            foreach($validation->messages()->getMessages() as $field_name => $messages)
            {
                $error_array[] = $messages;
            }
        }
        else
        {
            $ingredients = new IngredientTypes;            
            $ingredients->name =$request->ingredients_name;
            $ingredients->save();
            $success_output = 'Ingredients Type Added Successfully!';
        }
        $output = array(
            'error'     =>  $error_array,
            'success'   =>  $success_output
        );
        echo json_encode($output);
    }
    public function list()
    {
        $getingredientTypes = IngredientTypes::all();
        return view('theme.ingredienttypetable',compact('getingredientTypes'));
    }
    public function show(Request $request)
    {
        $getingredientTypes = IngredientTypes::where('id',$request->id)->first();

        return response()->json(['ResponseCode' => 1, 'ResponseText' => 'Ingredient type fetch successfully', 'ResponseData' => $getingredientTypes], 200);
    }
     public function update(Request $request)
    {

        $validation = Validator::make($request->all(),[
          'ingredients_name' => 'required',
        ]);

        $error_array = array();
        $success_output = '';
        if ($validation->fails())
        {
            foreach($validation->messages()->getMessages() as $field_name => $messages)
            {
                $error_array[] = $messages;
            }
        }
        else
        {
            $ingredients = new IngredientTypes;
            $ingredients->exists = true;
            $ingredients->id = $request->id;

            $ingredients->name =$request->ingredients_name;            
            $ingredients->save();           

            $success_output = 'Ingredients updated Successfully!';
        }
        $output = array(
            'error'     =>  $error_array,
            'success'   =>  $success_output
        );
        echo json_encode($output);
    }
    public function delete(Request $request)
    {
        $ingredients = IngredientTypes::where('id', $request->id)->delete();
        if ($ingredients) {
            return 1;
        } else {
            return 0;
        }
    }
}
