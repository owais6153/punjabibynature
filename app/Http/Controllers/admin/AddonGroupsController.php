<?php

namespace App\Http\Controllers\admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\AddonGroups;
use Validator;

class AddonGroupsController extends Controller
{
    public function index()
    {
        
        $addonGroups = AddonGroups::all();
        
        return view('addonGroups',compact('addonGroups'));
    }
    public function store(Request $request){

        $validation = Validator::make($request->all(),[
            'group_name' => 'required'            
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
            $addonGroups = new AddonGroups;            
            $addonGroups->name =$request->group_name;
            if ($request->group_price=='') {
                $addonGroups->price = 0;
            }
            else{
             $addonGroups->price =$request->group_price;   
            }
            
            $addonGroups->save();
            $success_output = 'Addon Group Added Successfully!';
        }
        $output = array(
            'error'     =>  $error_array,
            'success'   =>  $success_output
        );
        echo json_encode($output);
    }
    public function list()
    {
        $addonGroups = AddonGroups::all();
        return view('theme.addonGroupsTable',compact('addonGroups'));
    }
    public function show(Request $request)
    {
        $addonGroups = AddonGroups::where('id',$request->id)->first();

        return response()->json(['ResponseCode' => 1, 'ResponseText' => 'Addon group fetch successfully', 'ResponseData' => $addonGroups], 200);
    }
    public function update(Request $request)
    {

        $validation = Validator::make($request->all(),[
          'group_name' => 'required',
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
            $addonGroups = new AddonGroups;
            $addonGroups->exists = true;
            $addonGroups->id = $request->id;
            $addonGroups->name =$request->group_name;  
            if ($request->group_price=='') {
                $addonGroups->price= 0;
            }
            else{
                $addonGroups->price =$request->group_price; 
            }
                       
            $addonGroups->save();           

            $success_output = 'Addon Group updated Successfully!';
        }
        $output = array(
            'error'     =>  $error_array,
            'success'   =>  $success_output
        );
        echo json_encode($output);
    }
    public function delete(Request $request)
    {
        $addonGroups = AddonGroups::where('id', $request->id)->delete();
        if ($addonGroups) {
            return 1;
        } else {
            return 0;
        }
    }
}
