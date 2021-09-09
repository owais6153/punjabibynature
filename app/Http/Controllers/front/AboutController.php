<?php

namespace App\Http\Controllers\front;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\TermsCondition;
use App\About;
use App\Category;
use App\User;
use Validator;

class AboutController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $getabout = About::where('id','=','1')->first();
        $getdata=User::select('currency')->where('type','1')->first();
        $getcategory = Category::where('is_available','1')->where('is_deleted','2')->get();
        return view('aboutus',compact('getabout','getcategory','getdata'));
    }

    public function about()
    {
        $getdata=User::select('currency')->where('type','1')->first();
        $getabout = About::where('id','=','1')->first();
        $getcategory = Category::where('is_available','1')->where('is_deleted','2')->get();
        return view('front.about',compact('getabout','getdata','getcategory'));
    }
}
