<?php

namespace App\Http\Controllers\front;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\DB;
use App\Category;
use App\Item;
use App\Cart;
use App\Ratting;
use App\Slider;
use App\Banner;
use App\About;
use App\Contact;
use App\User;
use App\Pincode;
use Session;
use Validator;
use App\CateringCat;

class HomeController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */

    public function thankyou(){
        $getcategory = Category::where('is_available','=','1')->where('is_deleted','2')->get();
        $getabout = About::where('id','=','1')->first();
        if (Session::get('id')) {
            $user_id  = Session::get('id');
            $cartdata=Cart::with('itemimage')->select('id','qty','price','item_notes','cart.variation','item_name','tax',\DB::raw("CONCAT('".url('/storage/app/public/images/item/')."/', item_image) AS item_image"),'item_id','addons_id','addons_name','addons_price')
            ->where('user_id',$user_id)
            ->where('is_available','=','1')->get();
        }
        else{
            $cartdata_temp = Session::get('guest_cart');
            $cartdata = json_decode(json_encode($cartdata_temp));
        }
         $getdata=User::select('currency')->where('type','1')->first();
        return view('front.thankyou', compact('getcategory','cartdata', 'getabout', 'getdata'));
    }

    public function index()
    {
        $getslider = Slider::all();
        $getcategory = Category::where('is_available','=','1')->where('is_deleted','2')->get();
        $getabout = About::where('id','=','1')->first();
        $user_id  = Session::get('id');
        $taxval=User::select('currency','map')->where('type','1')->first();
        if (Session::get('id')) {
            $user_id  = Session::get('id');
            $cartdata=Cart::with('itemimage')->select('id','qty','price','item_notes','cart.variation','item_name','tax',\DB::raw("CONCAT('".url('/storage/app/public/images/item/')."/', item_image) AS item_image"),'item_id','addons_id','addons_name','addons_price')
            ->where('user_id',$user_id)
            ->where('is_available','=','1')->get();
        }
        else{
            $cartdata_temp = Session::get('guest_cart');
            $cartdata = json_decode(json_encode($cartdata_temp));
       
        //     // exit();
        }
        $getitem = Item::with(['category','itemimage','variation'])->select('item.cat_id','item.id','item.item_name','item.item_description',DB::raw('(case when favorite.item_id is null then 0 else 1 end) as is_favorite'))
        ->leftJoin('favorite', function($query) use($user_id) {
            $query->on('favorite.item_id','=','item.id')
            ->where('favorite.user_id', '=', $user_id);
        })
        ->where('item.item_status','1')
        ->where('item.is_deleted','2')
        ->orderby('cat_id')->get();
        $getreview = Ratting::with('users')->get();

        $getbanner = Banner::orderby('id','desc')->get();

        $getdata=User::select('currency')->where('type','1')->first();
        
        return view('front.home', compact('getslider','getcategory','getabout','getitem','getreview','getbanner','getdata','cartdata','taxval'));
    }
     public function contactus()
    {
        $getdata=User::select('currency')->where('type','1')->first();
        $getabout = About::where('id','=','1')->first();
        $getcategory = Category::where('is_available','1')->where('is_deleted','2')->get();
        if (Session::get('id')) {
            $user_id  = Session::get('id');
            $cartdata=Cart::with('itemimage')->select('id','qty','price','item_notes','cart.variation','item_name','tax',\DB::raw("CONCAT('".url('/storage/app/public/images/item/')."/', item_image) AS item_image"),'item_id','addons_id','addons_name','addons_price')
            ->where('user_id',$user_id)
            ->where('is_available','=','1')->get();
        }
        else{
            $cartdata_temp = Session::get('guest_cart');
            $cartdata = json_decode(json_encode($cartdata_temp));
       
        //     // exit();
        }
        return view('front.contactus',compact('getabout','getdata','getcategory','cartdata'));
    }
     
     public function fanclub()
    {

        $getdata=User::select('currency')->where('type','1')->first();
        $getabout = About::where('id','=','1')->first();
        $getcategory = Category::where('is_available','1')->where('is_deleted','2')->get();
        if (Session::get('id')) {
            $user_id  = Session::get('id');
            $cartdata=Cart::with('itemimage')->select('id','qty','price','item_notes','cart.variation','item_name','tax',\DB::raw("CONCAT('".url('/storage/app/public/images/item/')."/', item_image) AS item_image"),'item_id','addons_id','addons_name','addons_price')
            ->where('user_id',$user_id)
            ->where('is_available','=','1')->get();
        }
        else{
            $cartdata_temp = Session::get('guest_cart');
            $cartdata = json_decode(json_encode($cartdata_temp));
       
        //     // exit();
        }
        return view('front.fanclub',compact('getabout','getdata','getcategory','cartdata'));
    }
     public function catering()
    {
        $getdata=User::select('currency')->where('type','1')->first();
        $getabout = About::where('id','=','1')->first();
        $user_id  = Session::get('id');
        $getcategory = Category::where('is_available','1')->where('is_deleted','2')->get();
        $catering_category = CateringCat::select('catering_category.*')
        ->join("item","item.catering_cat_id","=","catering_category.id")
        ->groupBy('name')
        ->where('item.item_status', '1')
        ->where('item.is_deleted', '2')
        ->where('item.item_type', '=', 'catering')
        ->get();


        foreach ($catering_category as $key => $value) {
            $value->items = Item::with(['itemimage','variation'])->select('*')
            ->where('item.item_status', '1')
            ->where('item.is_deleted', '2')
            ->where('item.item_type', '=', 'catering')
            ->where('item.catering_cat_id', $value->id)
            ->get();
        }



        if (Session::get('id')) {
            $user_id = Session::get('id');
            $cartdata=Cart::with('itemimage')->select('id','qty','price','item_notes','cart.variation','item_name','tax',\DB::raw("CONCAT('".url('/storage/app/public/images/item/')."/', item_image) AS item_image"),'item_id','addons_id','addons_name','addons_price')
            ->where('user_id',$user_id)
            ->where('product_type','product')
            ->where('is_available','=','1')->get();
            $cateringcartdata=Cart::with('itemimage')->select('id','qty','price','item_notes','cart.variation','item_name','tax',\DB::raw("CONCAT('".url('/storage/app/public/images/item/')."/', item_image) AS item_image"),'item_id','addons_id','addons_name','addons_price')
            ->where('user_id',$user_id)
            ->where('product_type','catering')
            ->where('is_available','=','1')->get();
        }
        else{
            $cartdata_temp = Session::get('guest_cart');
            $cartdata = json_decode(json_encode($cartdata_temp));
            $cateringcartdata = json_decode(json_encode($cartdata_temp));
        }

        $taxval=User::select('currency','map')->where('type','1')->first();

        return view('front.catering',compact('getabout','getdata','getcategory','cartdata', 'catering_category', 'cateringcartdata', 'taxval'));
    }

    public function contact(Request $request)
    {
        if($request->firstname == ""){
            return response()->json(["status"=>0,"message"=>trans('messages.first_name_required')],200);
        }
        if($request->lastname == ""){
            return response()->json(["status"=>0,"message"=>trans('messages.last_name_required')],200);
        }
        if($request->email == ""){
            return response()->json(["status"=>0,"message"=>trans('messages.email_required')],200);
        }
        if($request->message == ""){
            return response()->json(["status"=>0,"message"=>trans('messages.message_required')],200);
        }
        $category = new Contact;
        $category->firstname =$request->firstname;
        $category->lastname =$request->lastname;
        $category->email =$request->email;
        $category->message =$request->message;
        $category->save();

        if ($category) {
            return response()->json(['status'=>1,'message'=>trans('messages.message_sent')],200);
        } else {
            return response()->json(['status'=>2,'message'=>trans('messages.wrong')],200);
        }

    }


    public function checkpincode(Request $request)
    {

        $getdata=User::select('min_order_amount','max_order_amount','currency')->where('type','1')
        ->get()->first();

        if($request->postal_code != ""){
            $pincode=Pincode::select('pincode')->where('pincode',$request->postal_code)
                        ->get()->first();
            if(@$pincode['pincode'] == $request->postal_code) {
                if(!empty($pincode))
                {
                    if ($getdata->min_order_amount > $request->order_total) {
                        return response()->json(['status'=>0,'message'=>"Order amount must be between ".$getdata->currency."".$getdata->min_order_amount." and ".$getdata->currency."".$getdata->max_order_amount.""],200);
                    } elseif ($getdata->max_order_amount < $request->order_total) {
                        return response()->json(['status'=>0,'message'=>"Order amount must be between ".$getdata->currency."".$getdata->min_order_amount." and ".$getdata->currency."".$getdata->max_order_amount.""],200);
                    } else {
                        return response()->json(['status'=>1,'message'=>trans('messages.available')],200);
                    }                
                }
            } else {
                return response()->json(['status'=>0,'message'=>trans('messages.delivery_unavailable')],200);
            }
        } else {
            
            if ($getdata->min_order_amount > $request->order_total) {
                return response()->json(['status'=>0,'message'=>"Order amount must be between ".$getdata->currency."".$getdata->min_order_amount." and ".$getdata->currency."".$getdata->max_order_amount.""],200);
            } elseif ($getdata->max_order_amount < $request->order_total) {
                return response()->json(['status'=>0,'message'=>"Order amount must be between ".$getdata->currency."".$getdata->min_order_amount." and ".$getdata->currency."".$getdata->max_order_amount.""],200);
            } else {
                return response()->json(['status'=>1,'message'=>'Ok'],200);
            }   
        }
    }
    public function notallow() {
        return view('front.405'); 
    }
}
