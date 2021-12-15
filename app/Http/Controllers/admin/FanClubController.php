<?php

namespace App\Http\Controllers\admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;
use App\FanClub;
use Validator;

class FanClubController extends Controller

{
    public function index()
   {
        $getfans = fanclub::all();
        return view('fanclub', compact('getfans'));        
   }
   public function store(Request $request)
    {
        $validation = Validator::make($request->all(),[
            'fan_name' => 'required',
            'fan_rating' => 'required',
            'fan_review' => 'required',
            'fan_link' => 'required'
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
            $fanclub = new fanclub;
            $fanclub->reviewer_name =$request->fan_name;
            $fanclub->reviewer_rating =$request->fan_rating;
            $fanclub->reviewer_review =$request->fan_review; 
            $fanclub->reviewer_link =$request->fan_link;                        
            $fanclub->save();

            $success_output = 'Fan added to FanClub Successfully!';
        }
        $output = array(
            'error'     =>  $error_array,
            'success'   =>  $success_output
        );
        echo json_encode($output);
    }
    public function fanlist()
    {
        $getfans = fanclub::all();
        return view('theme.fanclubTable',compact('getfans'));
    }    
}