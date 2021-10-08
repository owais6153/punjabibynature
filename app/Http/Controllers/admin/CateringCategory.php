<?php

namespace App\Http\Controllers\admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\CateringCat;
use Validator;

class CateringCategory extends Controller
{
	public function index()
	{
		$getcateringcat = CateringCat::all();
		return view('cateringCategory',compact('getcateringcat'));
	}
	public function store(Request $request)
    {
        $validation = Validator::make($request->all(),[
            'name' => 'required',
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
            $category = new CateringCat;
            $category->name = $request->name;
            $category->option_allowed =$request->option_allowed;
            $category->allowed_veg =$request->allowed_veg;
            $category->allowed_nonveg =$request->allowed_nonveg;
            $category->save();
            $success_output = 'Category Added Successfully!';
        }
        $output = array(
            'error'     =>  $error_array,
            'success'   =>  $success_output
        );
        echo json_encode($output);
    }
    public function show(Request $request)
    {
        $getcategory = CateringCat::where('id',$request->id)->first();
        return response()->json(['ResponseCode' => 1, 'ResponseText' => 'Category fetch successfully', 'ResponseData' => $getcategory], 200);
    }
    public function update(Request $request)
    {

         $validation = Validator::make($request->all(),[
            'name' => 'required',
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
            $category = new CateringCat;
            $category->exists = true;
            $category->id = $request->id;

            $category->name = $request->name;
            $category->option_allowed =$request->option_allowed;
            $category->allowed_veg =$request->allowed_veg;
            $category->allowed_nonveg =$request->allowed_nonveg;
            $category->save();


            $success_output = 'Category updated Successfully!';
        }
        $output = array(
            'error'     =>  $error_array,
            'success'   =>  $success_output
        );
        echo json_encode($output);
    }
    public function list()
    {
        $getcateringcat = CateringCat::all();
		return view('theme.cateringCategorytable',compact('getcateringcat'));
    }
    public function delete(Request $request)
    {
        $ComboItem = CateringCat::where('id', $request->id)->delete();
        if ($ComboItem) {
            return 1;
        } else {
            return 0;
        }
    }
}
