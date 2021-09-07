<?php

namespace App\Http\Controllers\admin;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Validation\Rule;
use App\Ingredients;
use App\IngredientTypes;
use Validator;

class IngredientsController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $getingredients = Ingredients::where('is_deleted','2')->get();

        $getingredientTypes = IngredientTypes::all();
        
        return view('ingredients',compact('getingredients', 'getingredientTypes'));
    }

    public function list()
    {
        $getingredients = Ingredients::where('is_deleted','2')->get();
        return view('theme.ingredientstable',compact('getingredients'));
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $s
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        $validation = Validator::make($request->all(),[
            'ingredients' => 'required',
            'image' => 'required|image|mimes:jpeg,png,jpg',
            'ingredients_type' => 'required'
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
            $image = 'ingredients-' . uniqid() . '.' . $request->image->getClientOriginalExtension();
            $request->image->move('storage/app/public/images/ingredients', $image);

            $ingredients = new Ingredients;
            $ingredients->image =$image;
            $ingredients->ingredients =$request->ingredients;
            $ingredients->type_id =$request->ingredients_type;
            $ingredients->save();
            $success_output = 'Ingredients Added Successfully!';
        }
        $output = array(
            'error'     =>  $error_array,
            'success'   =>  $success_output
        );
        echo json_encode($output);
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show(Request $request)
    {
        $getingredients = Ingredients::where('id',$request->id)->first();
        if($getingredients->image){
            $getingredients->img=url('storage/app/public/images/ingredients/'.$getingredients->image);
        }
        return response()->json(['ResponseCode' => 1, 'ResponseText' => 'Ingredients fetch successfully', 'ResponseData' => $getingredients], 200);
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit(Request $req)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request)
    {

        $validation = Validator::make($request->all(),[
          'ingredients' => 'required',
          'image' => 'image|mimes:jpeg,png,jpg',
          'ingredients_type' => 'required'
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
            $ingredients = new Ingredients;
            $ingredients->exists = true;
            $ingredients->id = $request->id;

            if(isset($request->image)){
                if($request->hasFile('image')){
                    $image = $request->file('image');
                    $image = 'ingredients-' . uniqid() . '.' . $request->image->getClientOriginalExtension();
                    $request->image->move('storage/app/public/images/ingredients', $image);
                    $ingredients->image=$image;

                    // unlink(public_path('images/ingredients/'.$request->old_img));
                }            
            }
            $ingredients->ingredients =$request->ingredients;
            $ingredients->type_id =$request->ingredients_type;
            $ingredients->save();           

            $success_output = 'Ingredients updated Successfully!';
        }
        $output = array(
            'error'     =>  $error_array,
            'success'   =>  $success_output
        );
        echo json_encode($output);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function status(Request $request)
    {
        $ingredients = Ingredients::where('id', $request->id)->update( array('is_available'=>$request->status) );
        if ($ingredients) {
            return 1;
        } else {
            return 0;
        }
    }

    public function delete(Request $request)
    {
        $ingredients = Ingredients::where('id', $request->id)->update( array('is_deleted'=>'1') );
        if ($ingredients) {
            return 1;
        } else {
            return 0;
        }
    }
}
