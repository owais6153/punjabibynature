<?php

namespace App\Http\Controllers\front;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\PrivacyPolicy;
use App\About;
use App\Category;
use App\User;
use Session;
use App\Cart;
use Validator;

class PrivacyPolicyController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $getprivacypolicy = PrivacyPolicy::where('id','1')->first();
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
        return view('privacy-policy',compact('getprivacypolicy','cartdata'));
    }

    public function privacy()
    {
        $getcategory = Category::where('is_available','=','1')->where('is_deleted','2')->get();
        $getdata=User::select('currency')->where('type','1')->first();
        $getabout = About::where('id','=','1')->first();
        $getprivacypolicy = PrivacyPolicy::where('id','1')->first();
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
        return view('front.privacy',compact('getprivacypolicy','getabout','getdata','getcategory','cartdata'));
    }
}
