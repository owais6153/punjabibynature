<?php

namespace App\Http\Controllers\admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;
use App\ComboItem;
use App\ComboGroup;
use Validator;

class ComboGroupController extends Controller
{
   public function index()
   {
        $getComboGroups = ComboGroup::all();
   		return view('comboGroup', compact( 'getComboGroups'));
   }
    public function store(Request $request)
    {
        $validation = Validator::make($request->all(),[
            'combo_group_name' => 'required',
            'price' => 'required'
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
            $ComboGroup = new ComboGroup;
            $ComboGroup->name =$request->combo_group_name; 
            $ComboGroup->price =$request->price;            
	        $ComboGroup->save();

            $success_output = 'Combo Group Added Successfully!';
        }
        $output = array(
            'error'     =>  $error_array,
            'success'   =>  $success_output
        );
        echo json_encode($output);
    }
    public function show(Request $request)
    {
        $getComboGroup = ComboGroup::where('id',$request->id)->first();
        return response()->json(['ResponseCode' => 1, 'ResponseText' => 'Combo Group fetch successfully', 'ResponseData' => $getComboGroup], 200);
    }

    public function update(Request $request)
    {

        $validation = Validator::make($request->all(),[
          'combo_group_name' => 'required',
          'price' => 'required'
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
            $ComboGroup = new ComboGroup;
            $ComboGroup->exists = true;
            $ComboGroup->id = $request->id;


            $ComboGroup->name =$request->combo_group_name;  
            $ComboGroup->price =$request->price;             
            $ComboGroup->save();           

            $success_output = 'Combo Group updated Successfully!';
        }
        $output = array(
            'error'     =>  $error_array,
            'success'   =>  $success_output
        );
        echo json_encode($output);
    }
    public function list()
    {
        $getComboGroups = ComboGroup::all();
        return view('theme.comboGroupTable',compact('getComboGroups'));
    }

    public function delete(Request $request)
    {
        $ComboGroup = ComboGroup::where('id', $request->id)->delete();
        if ($ComboGroup) {
            return 1;
        } else {
            return 0;
        }
    }
}
