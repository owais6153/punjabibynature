<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Category;
use Validator;

class CategoryController extends Controller
{
    public function category(Request $request)
    {
    	$categorydata=Category::select('id','category_name',\DB::raw("CONCAT('".url('/storage/app/public/images/category/')."/', image) AS image"))
        ->where('is_available','=','1')
        ->where('is_deleted','2')
        ->get();
        if(!empty($categorydata))
        {
        	return response()->json(['status'=>1,'message'=>'Category Successful','data'=>$categorydata],200);
        }
        else
        {
            return response()->json(['status'=>0,'message'=>trans('messages.no_data')],200);
        }
    }
}
