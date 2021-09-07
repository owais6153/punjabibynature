<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Pincode;
use Validator;

class PincodeController extends Controller
{
    public function pincode(Request $request)
    {
    	$pincodedata=Pincode::select('pincode')->orderby('id','desc')->get();
        if(!empty($pincodedata))
        {
        	return response()->json(['status'=>1,'message'=>'Pincode Successful','data'=>$pincodedata],200);
        }
        else
        {
            return response()->json(['status'=>0,'message'=>trans('messages.no_data')],200);
        }
    }
}
