<?php

namespace App\Http\Controllers\front;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\TermsCondition;
use App\About;
use App\Category;
use App\Cart;
use Session;
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
        if (Session::get('id')) {
            $cartdata=Cart::with('itemimage')->select('id','qty','price','item_notes','cart.variation','item_name','tax',\DB::raw("CONCAT('".url('/storage/app/public/images/item/')."/', item_image) AS item_image"),'item_id','addons_id','addons_name','addons_price')
            ->where('user_id',$user_id)
            ->where('is_available','=','1')->get();
        }
        else{
            $cartdata_temp = Session::get('guest_cart');
            $cartdata = json_decode(json_encode($cartdata_temp));
       
        //     // exit();
        }
        return view('terms-condition',compact('gettermscondition','cartdata'));

    }

    public function terms()
    {
        $getcategory = Category::where('is_available','=','1')->where('is_deleted','2')->get();
        $getdata=User::select('currency')->where('type','1')->first();
        $getabout = About::where('id','=','1')->first();
        $gettermscondition = TermsCondition::where('id','1')->first();
        if (Session::get('id')) {
            $cartdata=Cart::with('itemimage')->select('id','qty','price','item_notes','cart.variation','item_name','tax',\DB::raw("CONCAT('".url('/storage/app/public/images/item/')."/', item_image) AS item_image"),'item_id','addons_id','addons_name','addons_price')
            ->where('user_id',$user_id)
            ->where('is_available','=','1')->get();
        }
        else{
            $cartdata_temp = Session::get('guest_cart');
            $cartdata = json_decode(json_encode($cartdata_temp));
       
        //     // exit();
        }
        return view('front.terms',compact('gettermscondition','getabout','getdata','getcategory','cartdata'));
    }
}
