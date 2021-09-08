<?php

namespace App\Http\Controllers\admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;
use App\ComboItem;
use App\ComboGroup;
use Validator;

class ComboItemController extends Controller
{
   public function index()
   {
	   	$getComboItems = ComboItem::all();
        $getComboGroups = ComboGroup::all();
   		return view('comboItem', compact('getComboItems', 'getComboGroups'));
   }
    public function store(Request $request)
    {
        $validation = Validator::make($request->all(),[
            'combo_name' => 'required'
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


            $ComboItem = new ComboItem;
            $ComboItem->name =$request->combo_name;
            if ($request->group_id != '') {
                $ComboItem->group_id =$request->group_id;
            }
	        $ComboItem->save();

            $success_output = 'Combo item Added Successfully!';
        }
        $output = array(
            'error'     =>  $error_array,
            'success'   =>  $success_output
        );
        echo json_encode($output);
    }
    public function show(Request $request)
    {
        $getComboItems = ComboItem::where('id',$request->id)->first();
        return response()->json(['ResponseCode' => 1, 'ResponseText' => 'Combo Item fetch successfully', 'ResponseData' => $getComboItems], 200);
    }
    public function update(Request $request)
    {

        $validation = Validator::make($request->all(),[
          'combo_name' => 'required'
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
            $ComboItem = new ComboItem;
            $ComboItem->exists = true;
            $ComboItem->id = $request->id;


            $ComboItem->name =$request->combo_name;
             if ($request->group_id != '') {
                $ComboItem->group_id =$request->group_id;
            }
            $ComboItem->save();           

            $success_output = 'Combo updated Successfully!';
        }
        $output = array(
            'error'     =>  $error_array,
            'success'   =>  $success_output
        );
        echo json_encode($output);
    }
    public function list()
    {
        $getComboItems = ComboItem::all();
        return view('theme.comboItemsTable',compact('getComboItems'));
    }
    public function delete(Request $request)
    {
        $ComboItem = ComboItem::where('id', $request->id)->delete();
        if ($ComboItem) {
            return 1;
        } else {
            return 0;
        }
    }
}
