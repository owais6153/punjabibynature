<?php

namespace App\Http\Controllers\front;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Cart;
use App\Addons;
use App\Promocode;
use App\User;
use App\Category;
use App\About;
use App\Order;
use App\Item;
use Session;
use App\Time;
use App\Payment;
use App\Address;
use DateTime;

class CartController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index() {
        $user_id  = Session::get('id');
        $getcategory = Category::where('is_available','=','1')->where('is_deleted','2')->get();
        $getabout = About::where('id','=','1')->first();
        $getcategory = Category::where('is_available','=','1')->where('is_deleted','2')->get();
        $cartdata=Cart::with('itemimage')->select('id','qty','price','item_notes','cart.variation','item_name','tax',\DB::raw("CONCAT('".url('/storage/app/public/images/item/')."/', item_image) AS item_image"),'item_id','addons_id','addons_name','addons_price')
        ->where('user_id',$user_id)
        ->where('is_available','=','1')->get();


        if (Session::get('id')) {
            $cartdata=Cart::with('itemimage')->select('id','qty','price','item_notes','cart.variation','item_name','tax',\DB::raw("CONCAT('".url('/storage/app/public/images/item/')."/', item_image) AS item_image"),'item_id','addons_id','addons_name','addons_price')
            ->where('user_id',$user_id)
            ->where('is_available','=','1')->get();
        }
        else{
            $cartdata_temp = Session::get('guest_cart');
            $cartdata = json_decode(json_encode($cartdata_temp));
        }
        

        $getpromocode=Promocode::select('offer_name','offer_code','offer_amount','description')
        ->where('is_available','=','1')
        ->get();

        $addressdata=Address::where('user_id',Session::get('id'))->orderBy('id', 'DESC')->get();

        $userinfo=User::select('name','email','mobile','wallet')->where('id',$user_id)
        ->get()->first();

        $taxval=User::select('currency','map')->where('type','1')->first();

        $getdata=User::select('max_order_qty','min_order_amount','max_order_amount')->where('type','1')->first();

        $getpaymentdata=Payment::select('payment_name','test_public_key','live_public_key','environment')->where('is_available','1')->orderBy('id', 'DESC')->get();
//         echo "<pre>";
//         print_r($cartdata);
// exit();

        return view('front.cart', compact('cartdata','getabout','getpromocode','taxval','userinfo','getdata','getpaymentdata','addressdata','getcategory','getcategory'));
    }

    public function applypromocode(Request $request)
    {
        if($request->promocode == ""){
            return response()->json(["status"=>0,"message"=>trans('messages.promocode')],200);
        }

        $user_id  = Session::get('id');

        $checkpromo=Order::select('promocode')->where('promocode',$request->promocode)->where('user_id',$user_id)
        ->count();

        if ($checkpromo > "0" ) {
            return response()->json(['status'=>0,'message'=>trans('messages.once_per_user')],200);
        } else {
            $promocode=Promocode::select('promocode.offer_amount','promocode.description','promocode.offer_code')->where('promocode.offer_code',$request['promocode'])
            ->get()->first();

                session ( [ 
                    'offer_amount' => $promocode->offer_amount, 
                    'offer_code' => $promocode->offer_code,
                ] );

            if($promocode['offer_code']== $request->promocode) {
                if(!empty($promocode))
                {
                    return response()->json(['status'=>1,'message'=>trans('messages.promocode_applied'),'data'=>$promocode],200);
                }
            } else {
                return response()->json(['status'=>0,'message'=>trans('messages.wrong_promocode')],200);
            }
        }
    }

    public function removepromocode(Request $request)
    {
        
        $remove = session()->forget(['offer_amount','offer_code']);

        if(!$remove) {
            return response()->json(['status'=>1,'message'=>trans('messages.promocode_removed')],200);
        } else {
            return response()->json(['status'=>0,'message'=>trans('messages.wrong')],200);
        }
    }

    public function qtyupdate(Request $request)
    {
        if($request->cart_id == ""){
            return response()->json(["status"=>0,"message"=>trans('messages.cart_id_required')],400);
        }
        if($request->item_id == ""){
            return response()->json(["status"=>0,"message"=>trans('messages.item_required')],400);
        }
        if($request->qty == ""){
            return response()->json(["status"=>0,"message"=>trans('messages.qty_required')],400);
        }

        $data=Item::where('item.id', $request['item_id'])
        ->get()
        ->first();

        $cartdata=Cart::where('cart.id', $request['cart_id'])
        ->get()
        ->first();

        $getdata=User::select('max_order_qty','min_order_amount','max_order_amount')->where('type','1')
        ->get()->first();

        if ($getdata->max_order_qty < $request->qty) {
          return response()->json(['status'=>0,'message'=>trans('messages.maximum_purchase')],200);
        }

        $arr = explode(',', $cartdata->addons_id);
        $d = Addons::whereIn('id',$arr)->get();

        $sum = 0;
        foreach($d as $key => $value) {
            $sum += $value->price; 
        }

        if ($request->type == "decreaseValue") {
            $qty = $cartdata->qty-1;
        } else {
            $qty = $cartdata->qty+1;
        }

        $update=Cart::where('id',$request['cart_id'])->update(['item_id'=>$request->item_id,'qty'=>$qty]);

        return response()->json(['status'=>1,'message'=>trans('messages.qty_update')],200);
    }

    public function deletecartitem(Request $request)
    {
        if($request->cart_id == ""){
            return response()->json(["status"=>0,"message"=>trans('messages.cart_id_required')],400);
        }

        $cart=Cart::where('id', $request->cart_id)->delete();

        $count=Cart::where('user_id',Session::get('id'))->count();
        
        Session::put('cart', $count);
        if($cart)
        {
            return 1;
        }
        else
        {
            return 2;
        }
    }

    public function isopenclose(Request $request)
    {
        $getdata=User::select('timezone')->where('type','1')->first();
        date_default_timezone_set($getdata->timezone);

        $date = date('Y/m/d h:i:sa');
        $day = date('l', strtotime($date));

        $isopenclose=Time::where('day','=',$day)->first();

        $current_time = DateTime::createFromFormat('H:i a', date("h:i a"));
        $open_time = DateTime::createFromFormat('H:i a', $isopenclose->open_time);
        $close_time = DateTime::createFromFormat('H:i a', $isopenclose->close_time);

        if ($current_time > $open_time && $current_time < $close_time && $isopenclose->always_close == "2") {
           return response()->json(['status'=>1,'message'=>trans('messages.restaurant_open')],200);
        } else {
           return response()->json(['status'=>0,'message'=>trans('messages.restaurant_closed')],200);
        }
    }
}
