<?php

namespace App\Http\Controllers\front;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\TermsCondition;
use App\About;
use App\Category;
use App\User;
use Validator;

class TermsController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $gettermscondition = TermsCondition::where('id','1')->first();
        return view('terms-condition',compact('gettermscondition'));
    }

    public function terms()
    {
        $getcategory = Category::where('is_available','=','1')->where('is_deleted','2')->get();
        $getdata=User::select('currency')->where('type','1')->first();
        $getabout = About::where('id','=','1')->first();
        $gettermscondition = TermsCondition::where('id','1')->first();
        return view('front.terms',compact('gettermscondition','getabout','getdata','getcategory'));
    }
}
