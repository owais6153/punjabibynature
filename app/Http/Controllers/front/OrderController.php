<?php

namespace App\Http\Controllers\front;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Mail;
use App\OrderDetails;
use App\Order;
use App\About;
use App\Transaction;
use App\User;
use App\Cart;
use App\Addons;
use App\Promocode;
use App\Pincode;
use Session;

class OrderController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index() {
        $getabout = About::where('id','=','1')->first();
        $orderdata=OrderDetails::select('order.order_total as total_price',DB::raw('SUM(order_details.qty) AS qty'),'order.id',DB::raw('DATE_FORMAT(order.created_at, "%d %M %Y") as date'),'order.order_number','order.order_type','order.status','order.payment_type')
        ->join('item','order_details.item_id','=','item.id')
        ->join('order','order_details.order_id','=','order.id')
        ->where('order.user_id',Session::get('id'))->groupBy('order_details.order_id')->orderby('order.id','desc')->paginate(9);
        
        $getdata=User::select('currency')->where('type','1')->first();
        return view('front.orders',compact('orderdata','getabout','getdata'));
    }

    public function orderdetails(Request $request) {
        $getabout = About::where('id','=','1')->first();
        $orderdata=OrderDetails::with('itemimage')->select('order_details.id','order_details.qty','order_details.price as total_price','item.id','order_details.item_name',\DB::raw("CONCAT('".url('/storage/app/public/images/item/')."/', order_details.item_image) AS item_image"),'order_details.variation','order_details.variation_price','order_details.item_id','order_details.addons_id','order_details.addons_name','order_details.addons_price','order_details.item_notes')
        ->join('item','order_details.item_id','=','item.id')
        ->join('order','order_details.order_id','=','order.id')
        ->where('order_details.order_id',$request->id)->get();

        if(count($orderdata) == 0){ 
            abort(404); 
        } else {
            foreach ($orderdata as $value) {
               $arr = explode(',', $value['addons_id']);
               $value['addons']=Addons::whereIn('id',$arr)->get();
            };
            
            $status=Order::select('order.driver_id','order.order_number',DB::raw('DATE_FORMAT(order.created_at, "%d %M %Y") as date'),'order.address','order.building','order.landmark','order.pincode','order.order_type','order.promocode','order.id','order.discount_amount','order.order_number','order.status','order.order_notes','order.tax','order.tax_amount','order.delivery_charge')->where('order.id',$request->id)
            ->get()->first();

            $getdriver=User::select('users.name',\DB::raw("CONCAT('".url('/storage/app/public/images/profile/')."/', users.profile_image) AS profile_image"),'users.mobile')->where('users.id',$status->driver_id)
            ->get()->first();

            if (@$getdriver["name"] == "") {
                $drivername = "";
                $driverprofile_image = "";
                $drivermobile = "";
            } else {
                $drivername = $getdriver["name"];
                $driverprofile_image = $getdriver["profile_image"];
                $drivermobile = $getdriver["mobile"];
            }

            $summery = array(
                'id' => "$status->id",
                'tax' => "$status->tax",
                'tax_amount' => "$status->tax_amount",
                'discount_amount' => $status->discount_amount,
                'order_number' => $status->order_number,
                'created_at' => $status->date,
                'promocode' => $status->promocode,
                'delivery_charge' => "$status->delivery_charge",
                'address' => $status->address,
                'building' => $status->building,
                'landmark' => $status->landmark,
                'pincode' => $status->pincode,
                'order_notes' => $status->order_notes,
                'status' => $status->status,
                'order_type' => $status->order_type,
                'driver_name' => $drivername,
                'driver_profile_image' => $driverprofile_image,
                'driver_mobile' => $drivermobile,
            );
        }

        $getdata=User::select('currency')->where('type','1')->first();
        return view('front.order-details',compact('orderdata','summery','getabout','getdata'));
    }

    public function cashondelivery(Request $request)
    {
        if ($request->order_type == "1") {
            if($request->address == ""){
                return response()->json(["status"=>0,"message"=>trans('messages.address_required')],200);
            }

            if($request->lat == ""){
                return response()->json(["status"=>0,"message"=>trans('messages.select_address')],200);
            }

            if($request->lang == ""){
                return response()->json(["status"=>0,"message"=>trans('messages.select_address')],200);
            }

            if($request->postal_code == ""){
                return response()->json(["status"=>0,"message"=>trans('messages.pincode_required')],200);
            }

            if($request->building == ""){
                return response()->json(["status"=>0,"message"=>trans('messages.no_required')],200);
            }

            if($request->landmark == ""){
                return response()->json(["status"=>0,"message"=>trans('messages.landmark_required')],200);
            }
        } 

        

        $getuserdata=User::where('id',Session::get('id'))
        ->get()->first(); 

        $getdata=User::select('token','firebase','min_order_amount','max_order_amount','currency','timezone')->where('type','1')
        ->get()->first();

        date_default_timezone_set($getdata->timezone);

        if ($request->discount_amount == "NaN") {
            $discount_amount = "0.00";
        } else {
            $discount_amount = $request->discount_amount;
        }     

        try {

            if ($request->order_type == "2") {

                $order_number = substr(str_shuffle(str_repeat("0123456789ABCDEFGHIJKLMNOPQRSTUVWXYZ", 10)), 0, 10);

                if ($request->order_type == "2") {
                    $delivery_charge = "0.00";
                    $address = "";
                    $lat = "";
                    $lang = "";
                    $building = "";
                    $landmark = "";
                    $postal_code = "";
                    $order_total = $request->order_total-$request->$delivery_charge;
                } else {
                    $delivery_charge = $request->delivery_charge;
                    $address = $request->address;
                    $lat = $request->lat;
                    $lang = $request->lang;
                    $order_total = $request->order_total;
                    $building = $request->building;
                    $landmark = $request->landmark;
                    $postal_code = $request->postal_code;
                }

                if ($getdata->min_order_amount > $request->total_order) {
                    return response()->json(['status'=>0,'message'=>"Order amount must be between ".$getdata->currency."".$getdata->min_order_amount." and ".$getdata->currency."".$getdata->max_order_amount.""],200);
                }

                if ($getdata->max_order_amount < $request->total_order) {
                    return response()->json(['status'=>0,'message'=>"Order amount must be between ".$getdata->currency."".$getdata->min_order_amount." and ".$getdata->currency."".$getdata->max_order_amount.""],200);
                }

                $order = new Order;
                $order->order_number =$order_number;
                $order->user_id =Session::get('id');
                $order->order_total =$order_total;
                $order->payment_type ='0';
                $order->status ='1';
                $order->address =$address;
                $order->promocode =$request->promocode;
                $order->discount_amount =$discount_amount;
                $order->discount_pr =$request->discount_pr;
                $order->tax =$request->tax;
                $order->tax_amount =$request->tax_amount;
                $order->delivery_charge =$delivery_charge;
                $order->order_type =$request->order_type;
                $order->lat =$lat;
                $order->lang =$lang;
                $order->building =$building;
                $order->landmark =$landmark;
                $order->pincode =$postal_code;
                $order->order_notes =$request->notes;
                $order->order_from ='web';

                $order->save();

                $order_id = DB::getPdo()->lastInsertId();
                $data=Cart::where('cart.user_id',Session::get('id'))
                ->get();
                foreach ($data as $value) {
                    $OrderPro = new OrderDetails;
                    $OrderPro->order_id = $order_id;
                    $OrderPro->user_id = $value['user_id'];
                    $OrderPro->item_id = $value['item_id'];
                    $OrderPro->item_name = $value['item_name'];
                    $OrderPro->item_image = $value['item_image'];
                    $OrderPro->addons_price = $value['addons_price'];
                    $OrderPro->addons_name = $value['addons_name'];
                    $OrderPro->price = $value['price'];
                    $OrderPro->variation_id = $value['variation_id'];
                    $OrderPro->variation_price = $value['variation_price'];
                    $OrderPro->variation = $value['variation'];
                    $OrderPro->qty = $value['qty'];
                    $OrderPro->item_notes = $value['item_notes'];
                    $OrderPro->addons_id = $value['addons_id'];
                    $OrderPro->save();
                }
                $cart=Cart::where('user_id', Session::get('id'))->delete();

                $count=Cart::where('user_id',Session::get('id'))->count();

                try{
                    $admintitle = "Order";
                    $adminbody = 'You have received a new order '.$order_number.'';
                    $admingoogle_api_key = $getdata->firebase; 
                    
                    $adminregistrationIds = $getdata->token;
                    #prep the bundle
                    $adminmsg = array
                        (
                        'body'  => $adminbody,
                        'title' => $admintitle,
                        'sound' => 1/*Default sound*/
                        );
                    $adminfields = array
                        (
                        'to'            => $adminregistrationIds,
                        'notification'  => $adminmsg
                        );
                    $adminheaders = array
                        (
                        'Authorization: key=' . $admingoogle_api_key,
                        'Content-Type: application/json'
                        );
                    #Send Reponse To FireBase Server
                    $adminch = curl_init();
                    curl_setopt( $adminch,CURLOPT_URL, 'https://fcm.googleapis.com/fcm/send' );
                    curl_setopt( $adminch,CURLOPT_POST, true );
                    curl_setopt( $adminch,CURLOPT_HTTPHEADER, $adminheaders );
                    curl_setopt( $adminch,CURLOPT_RETURNTRANSFER, true );
                    curl_setopt( $adminch,CURLOPT_SSL_VERIFYPEER, false );
                    curl_setopt( $adminch,CURLOPT_POSTFIELDS, json_encode( $adminfields ) );

                    $result = curl_exec ( $adminch );
                    curl_close ( $adminch );
                }catch(\Swift_TransportException $e){
                    $response = $e->getMessage() ;
                    return response()->json(['status'=>0,'message'=>trans('messages.notification_error')],200);
                }

                try{
                    $getlogo = About::select('logo')->where('id','=','1')->first();
                    $getusers = Order::with('users')->where('order.id', $order_id)->get()->first();

                    $getorders=OrderDetails::with('itemimage')->select('order_details.id','order_details.qty','order_details.price as total_price','item.id','item.item_name','order_details.item_id','order_details.addons_id','order_details.item_notes','order_details.variation_price')
                    ->join('item','order_details.item_id','=','item.id')
                    ->join('order','order_details.order_id','=','order.id')
                    ->where('order_details.order_id',$order_id)->get()->toArray();

                    $arrayName = array();
                    foreach ($getorders as $key => $value) {
                       $arr = explode(',', $value['addons_id']);
                       $arrayName[$key]=$value;
                       $arrayName[$key]['addons']=Addons::whereIn('id',$arr)->get()->toArray();
                    }; 
                    
                    $email=$getuserdata->email;
                    $name=$getuserdata->name;
                    $logo=$getlogo->logo;
                    $currency=$getdata->currency;

                    $data=['getusers'=>$getusers,'getorders'=>$arrayName,'email'=>$email,'name'=>$name,'logo'=>$logo,'currency'=>$currency];
                    Mail::send('Email.emailinvoice',$data,function($message)use($data){
                        $message->from(env('MAIL_USERNAME'))->subject('Order');
                        $message->to($data['email']);
                    } );
                }catch(\Swift_TransportException $e){
                    $response = $e->getMessage() ;
                    return response()->json(['status'=>0,'message'=>trans('messages.email_error')],200);
                }
                
                Session::put('cart', $count);

                session()->forget(['offer_amount','offer_code']);
                
                return response()->json(['status'=>1,'message'=>trans('messages.order_placed')],200);
            } else {
                $pincode=Pincode::select('pincode')->where('pincode',$request->postal_code)
                ->get()->first();

                if(@$pincode['pincode'] == $request->postal_code) {
                    if(!empty($pincode))
                    {
                        $order_number = substr(str_shuffle(str_repeat("0123456789ABCDEFGHIJKLMNOPQRSTUVWXYZ", 10)), 0, 10);

                        if ($request->order_type == "2") {
                            $delivery_charge = "0.00";
                            $address = "";
                            $lat = "";
                            $lang = "";
                            $building = "";
                            $landmark = "";
                            $postal_code = "";
                            $order_total = $request->order_total-$request->$delivery_charge;
                        } else {
                            $delivery_charge = $request->delivery_charge;
                            $address = $request->address;
                            $lat = $request->lat;
                            $lang = $request->lang;
                            $order_total = $request->order_total;
                            $building = $request->building;
                            $landmark = $request->landmark;
                            $postal_code = $request->postal_code;
                        }

                        if ($getdata->min_order_amount > $request->total_order) {
                            return response()->json(['status'=>0,'message'=>"Order amount must be between ".$getdata->currency."".$getdata->min_order_amount." and ".$getdata->currency."".$getdata->max_order_amount.""],200);
                        }

                        if ($getdata->max_order_amount < $request->total_order) {
                            return response()->json(['status'=>0,'message'=>"Order amount must be between ".$getdata->currency."".$getdata->min_order_amount." and ".$getdata->currency."".$getdata->max_order_amount.""],200);
                        }

                        $order = new Order;
                        $order->order_number =$order_number;
                        $order->user_id =Session::get('id');
                        $order->order_total =$order_total;
                        $order->payment_type ='0';
                        $order->status ='1';
                        $order->address =$address;
                        $order->promocode =$request->promocode;
                        $order->discount_amount =$discount_amount;
                        $order->discount_pr =$request->discount_pr;
                        $order->tax =$request->tax;
                        $order->tax_amount =$request->tax_amount;
                        $order->delivery_charge =$delivery_charge;
                        $order->order_type =$request->order_type;
                        $order->lat =$lat;
                        $order->lang =$lang;
                        $order->building =$building;
                        $order->landmark =$landmark;
                        $order->pincode =$postal_code;
                        $order->order_notes =$request->notes;
                        $order->order_from ='web';

                        $order->save();

                        $order_id = DB::getPdo()->lastInsertId();
                        $data=Cart::where('cart.user_id',Session::get('id'))
                        ->get();
                        foreach ($data as $value) {
                            $OrderPro = new OrderDetails;
                            $OrderPro->order_id = $order_id;
                            $OrderPro->user_id = $value['user_id'];
                            $OrderPro->item_id = $value['item_id'];
                            $OrderPro->item_name = $value['item_name'];
                            $OrderPro->item_image = $value['item_image'];
                            $OrderPro->addons_price = $value['addons_price'];
                            $OrderPro->addons_name = $value['addons_name'];
                            $OrderPro->price = $value['price'];
                            $OrderPro->variation_id = $value['variation_id'];
                            $OrderPro->variation_price = $value['variation_price'];
                            $OrderPro->variation = $value['variation'];
                            $OrderPro->qty = $value['qty'];
                            $OrderPro->item_notes = $value['item_notes'];
                            $OrderPro->addons_id = $value['addons_id'];
                            $OrderPro->save();
                        }
                        $cart=Cart::where('user_id', Session::get('id'))->delete();

                        $count=Cart::where('user_id',Session::get('id'))->count();

                        try{
                            $admintitle = "Order";
                            $adminbody = 'You have received a new order '.$order_number.'';
                            $admingoogle_api_key = $getdata->firebase; 
                            
                            $adminregistrationIds = $getdata->token;
                            #prep the bundle
                            $adminmsg = array
                                (
                                'body'  => $adminbody,
                                'title' => $admintitle,
                                'sound' => 1/*Default sound*/
                                );
                            $adminfields = array
                                (
                                'to'            => $adminregistrationIds,
                                'notification'  => $adminmsg
                                );
                            $adminheaders = array
                                (
                                'Authorization: key=' . $admingoogle_api_key,
                                'Content-Type: application/json'
                                );
                            #Send Reponse To FireBase Server
                            $adminch = curl_init();
                            curl_setopt( $adminch,CURLOPT_URL, 'https://fcm.googleapis.com/fcm/send' );
                            curl_setopt( $adminch,CURLOPT_POST, true );
                            curl_setopt( $adminch,CURLOPT_HTTPHEADER, $adminheaders );
                            curl_setopt( $adminch,CURLOPT_RETURNTRANSFER, true );
                            curl_setopt( $adminch,CURLOPT_SSL_VERIFYPEER, false );
                            curl_setopt( $adminch,CURLOPT_POSTFIELDS, json_encode( $adminfields ) );

                            $result = curl_exec ( $adminch );
                            curl_close ( $adminch );
                        }catch(\Swift_TransportException $e){
                            $response = $e->getMessage() ;
                            return response()->json(['status'=>0,'message'=>trans('messages.notification_error')],200);
                        }

                        try{
                            $getlogo = About::select('logo')->where('id','=','1')->first();
                            $getusers = Order::with('users')->where('order.id', $order_id)->get()->first();
                            $getorders=OrderDetails::with('itemimage')->select('order_details.id','order_details.qty','order_details.price as total_price','item.id','item.item_name','order_details.item_id','order_details.addons_id','order_details.item_notes','order_details.variation_price')
                            ->join('item','order_details.item_id','=','item.id')
                            ->join('order','order_details.order_id','=','order.id')
                            ->where('order_details.order_id',$order_id)->get()->toArray();

                            $arrayName = array();
                            foreach ($getorders as $key => $value) {
                               $arr = explode(',', $value['addons_id']);
                               $arrayName[$key]=$value;
                               $arrayName[$key]['addons']=Addons::whereIn('id',$arr)->get()->toArray();
                            }; 
                            
                            $email=$getuserdata->email;
                            $name=$getuserdata->name;
                            $logo=$getlogo->logo;
                            $currency=$getdata->currency;

                            $data=['getusers'=>$getusers,'getorders'=>$arrayName,'email'=>$email,'name'=>$name,'logo'=>$logo,'currency'=>$currency];
                            Mail::send('Email.emailinvoice',$data,function($message)use($data){
                                $message->from(env('MAIL_USERNAME'))->subject('Order');
                                $message->to($data['email']);
                            } );
                        }catch(\Swift_TransportException $e){
                            $response = $e->getMessage() ;
                            return response()->json(['status'=>0,'message'=>trans('messages.email_error')],200);
                        }
                        
                        Session::put('cart', $count);

                        session()->forget(['offer_amount','offer_code']);
                        
                        return response()->json(['status'=>1,'message'=>trans('messages.order_placed')],200);
                    }
                } else {
                    return response()->json(['status'=>0,'message'=>trans('messages.delivery_unavailable')],200);
                }
            }
            

        } catch (\Exception $e) {
            return  $e->getMessage();
            \Session::put('error',$e->getMessage());
            return redirect()->back();
        }
    }

    public function walletorder(Request $request)
    {

        $getuserdata=User::where('id',Session::get('id'))
        ->get()->first(); 

        if ($request->order_type == "1") {
            if($request->address == ""){
                return response()->json(["status"=>0,"message"=>trans('messages.address_required')],200);
            }

            if($request->lat == ""){
                return response()->json(["status"=>0,"message"=>trans('messages.select_address')],200);
            }

            if($request->lang == ""){
                return response()->json(["status"=>0,"message"=>trans('messages.select_address')],200);
            }

            if($request->postal_code == ""){
                return response()->json(["status"=>0,"message"=>trans('messages.pincode_required')],200);
            }

            if($request->building == ""){
                return response()->json(["status"=>0,"message"=>trans('messages.no_required')],200);
            }

            if($request->landmark == ""){
                return response()->json(["status"=>0,"message"=>trans('messages.landmark_required')],200);
            }
        } 

        if ($getuserdata->wallet < $request->order_total) {
            return response()->json(["status"=>0,"message"=>trans('messages.low_balance')],200);
        }

        $getdata=User::select('token','firebase','min_order_amount','max_order_amount','currency','timezone')->where('type','1')
        ->get()->first();

        date_default_timezone_set($getdata->timezone);

        if ($request->discount_amount == "NaN") {
            $discount_amount = "0.00";
        } else {
            $discount_amount = $request->discount_amount;
        }     

        try {

            if ($request->order_type == "2") {

                $order_number = substr(str_shuffle(str_repeat("0123456789ABCDEFGHIJKLMNOPQRSTUVWXYZ", 10)), 0, 10);

                if ($request->order_type == "2") {
                    $delivery_charge = "0.00";
                    $address = "";
                    $lat = "";
                    $lang = "";
                    $building = "";
                    $landmark = "";
                    $postal_code = "";
                    $order_total = $request->order_total-$request->$delivery_charge;
                } else {
                    $delivery_charge = $request->delivery_charge;
                    $address = $request->address;
                    $lat = $request->lat;
                    $lang = $request->lang;
                    $order_total = $request->order_total;
                    $building = $request->building;
                    $landmark = $request->landmark;
                    $postal_code = $request->postal_code;
                }

                if ($getdata->min_order_amount > $request->total_order) {
                    return response()->json(['status'=>0,'message'=>"Order amount must be between ".$getdata->currency."".$getdata->min_order_amount." and ".$getdata->currency."".$getdata->max_order_amount.""],200);
                }

                if ($getdata->max_order_amount < $request->total_order) {
                    return response()->json(['status'=>0,'message'=>"Order amount must be between ".$getdata->currency."".$getdata->min_order_amount." and ".$getdata->currency."".$getdata->max_order_amount.""],200);
                }

                $order = new Order;
                $order->order_number =$order_number;
                $order->user_id =Session::get('id');
                $order->order_total =$order_total;
                $order->payment_type ='3';
                $order->status ='1';
                $order->address =$address;
                $order->promocode =$request->promocode;
                $order->discount_amount =$discount_amount;
                $order->discount_pr =$request->discount_pr;
                $order->tax =$request->tax;
                $order->tax_amount =$request->tax_amount;
                $order->delivery_charge =$delivery_charge;
                $order->order_type =$request->order_type;
                $order->lat =$lat;
                $order->lang =$lang;
                $order->building =$building;
                $order->landmark =$landmark;
                $order->pincode =$postal_code;
                $order->order_notes =$request->notes;
                $order->order_from ='web';

                $order->save();

                $order_id = DB::getPdo()->lastInsertId();
                $data=Cart::where('cart.user_id',Session::get('id'))
                ->get();
                foreach ($data as $value) {
                    $OrderPro = new OrderDetails;
                    $OrderPro->order_id = $order_id;
                    $OrderPro->user_id = $value['user_id'];
                    $OrderPro->item_id = $value['item_id'];
                    $OrderPro->item_name = $value['item_name'];
                    $OrderPro->item_image = $value['item_image'];
                    $OrderPro->addons_price = $value['addons_price'];
                    $OrderPro->addons_name = $value['addons_name'];
                    $OrderPro->price = $value['price'];
                    $OrderPro->variation_id = $value['variation_id'];
                    $OrderPro->variation_price = $value['variation_price'];
                    $OrderPro->variation = $value['variation'];
                    $OrderPro->qty = $value['qty'];
                    $OrderPro->item_notes = $value['item_notes'];
                    $OrderPro->addons_id = $value['addons_id'];
                    $OrderPro->save();
                }
                $cart=Cart::where('user_id', Session::get('id'))->delete();

                $count=Cart::where('user_id',Session::get('id'))->count();

                $wallet = $getuserdata->wallet - $order_total;

                $UpdateWalletDetails = User::where('id', Session::get('id'))
                ->update(['wallet' => $wallet]);

                $Wallet = new Transaction;
                $Wallet->user_id = Session::get('id');
                $Wallet->order_id = $order_id;
                $Wallet->order_number = $order_number;
                $Wallet->wallet = $order_total;
                $Wallet->payment_id = NULL;
                $Wallet->order_type = $request->order_type;
                $Wallet->transaction_type = '2';
                $Wallet->save();

                try{
                    $admintitle = "Order";
                    $adminbody = 'You have received a new order '.$order_number.'';
                    $admingoogle_api_key = $getdata->firebase; 
                    
                    $adminregistrationIds = $getdata->token;
                    #prep the bundle
                    $adminmsg = array
                        (
                        'body'  => $adminbody,
                        'title' => $admintitle,
                        'sound' => 1/*Default sound*/
                        );
                    $adminfields = array
                        (
                        'to'            => $adminregistrationIds,
                        'notification'  => $adminmsg
                        );
                    $adminheaders = array
                        (
                        'Authorization: key=' . $admingoogle_api_key,
                        'Content-Type: application/json'
                        );
                    #Send Reponse To FireBase Server
                    $adminch = curl_init();
                    curl_setopt( $adminch,CURLOPT_URL, 'https://fcm.googleapis.com/fcm/send' );
                    curl_setopt( $adminch,CURLOPT_POST, true );
                    curl_setopt( $adminch,CURLOPT_HTTPHEADER, $adminheaders );
                    curl_setopt( $adminch,CURLOPT_RETURNTRANSFER, true );
                    curl_setopt( $adminch,CURLOPT_SSL_VERIFYPEER, false );
                    curl_setopt( $adminch,CURLOPT_POSTFIELDS, json_encode( $adminfields ) );

                    $result = curl_exec ( $adminch );
                    curl_close ( $adminch );
                }catch(\Swift_TransportException $e){
                    $response = $e->getMessage() ;
                    return response()->json(['status'=>0,'message'=>trans('messages.notification_error')],200);
                }

                try{
                    $getlogo = About::select('logo')->where('id','=','1')->first();
                    $getusers = Order::with('users')->where('order.id', $order_id)->get()->first();
                    $getorders=OrderDetails::with('itemimage')->select('order_details.id','order_details.qty','order_details.price as total_price','item.id','item.item_name','order_details.item_id','order_details.addons_id','order_details.item_notes','order_details.variation_price')
                    ->join('item','order_details.item_id','=','item.id')
                    ->join('order','order_details.order_id','=','order.id')
                    ->where('order_details.order_id',$order_id)->get()->toArray();

                    $arrayName = array();
                    foreach ($getorders as $key => $value) {
                       $arr = explode(',', $value['addons_id']);
                       $arrayName[$key]=$value;
                       $arrayName[$key]['addons']=Addons::whereIn('id',$arr)->get()->toArray();
                    }; 
                    
                    $email=$getuserdata->email;
                    $name=$getuserdata->name;
                    $logo=$getlogo->logo;
                    $currency=$getdata->currency;

                    $data=['getusers'=>$getusers,'getorders'=>$arrayName,'email'=>$email,'name'=>$name,'logo'=>$logo,'currency'=>$currency];
                    Mail::send('Email.emailinvoice',$data,function($message)use($data){
                        $message->from(env('MAIL_USERNAME'))->subject('Order');
                        $message->to($data['email']);
                    } );
                }catch(\Swift_TransportException $e){
                    $response = $e->getMessage() ;
                    return response()->json(['status'=>0,'message'=>trans('messages.email_error')],200);
                }
                
                Session::put('cart', $count);

                session()->forget(['offer_amount','offer_code']);
                
                return response()->json(['status'=>1,'message'=>trans('messages.order_placed')],200);
            } else {
                $pincode=Pincode::select('pincode')->where('pincode',$request->postal_code)
                ->get()->first();

                if(@$pincode['pincode'] == $request->postal_code) {
                    if(!empty($pincode))
                    {
                        $order_number = substr(str_shuffle(str_repeat("0123456789ABCDEFGHIJKLMNOPQRSTUVWXYZ", 10)), 0, 10);

                        if ($request->order_type == "2") {
                            $delivery_charge = "0.00";
                            $address = "";
                            $lat = "";
                            $lang = "";
                            $building = "";
                            $landmark = "";
                            $postal_code = "";
                            $order_total = $request->order_total-$request->$delivery_charge;
                        } else {
                            $delivery_charge = $request->delivery_charge;
                            $address = $request->address;
                            $lat = $request->lat;
                            $lang = $request->lang;
                            $order_total = $request->order_total;
                            $building = $request->building;
                            $landmark = $request->landmark;
                            $postal_code = $request->postal_code;
                        }

                        if ($getdata->min_order_amount > $request->total_order) {
                            return response()->json(['status'=>0,'message'=>"Order amount must be between ".$getdata->currency."".$getdata->min_order_amount." and ".$getdata->currency."".$getdata->max_order_amount.""],200);
                        }

                        if ($getdata->max_order_amount < $request->total_order) {
                            return response()->json(['status'=>0,'message'=>"Order amount must be between ".$getdata->currency."".$getdata->min_order_amount." and ".$getdata->currency."".$getdata->max_order_amount.""],200);
                        }

                        $order = new Order;
                        $order->order_number =$order_number;
                        $order->user_id =Session::get('id');
                        $order->order_total =$order_total;
                        $order->payment_type ='3';
                        $order->status ='1';
                        $order->address =$address;
                        $order->promocode =$request->promocode;
                        $order->discount_amount =$discount_amount;
                        $order->discount_pr =$request->discount_pr;
                        $order->tax =$request->tax;
                        $order->tax_amount =$request->tax_amount;
                        $order->delivery_charge =$delivery_charge;
                        $order->order_type =$request->order_type;
                        $order->lat =$lat;
                        $order->lang =$lang;
                        $order->building =$building;
                        $order->landmark =$landmark;
                        $order->pincode =$postal_code;
                        $order->order_notes =$request->notes;
                        $order->order_from ='web';

                        $order->save();

                        $order_id = DB::getPdo()->lastInsertId();
                        $data=Cart::where('cart.user_id',Session::get('id'))
                        ->get();
                        foreach ($data as $value) {
                            $OrderPro = new OrderDetails;
                            $OrderPro->order_id = $order_id;
                            $OrderPro->user_id = $value['user_id'];
                            $OrderPro->item_id = $value['item_id'];
                            $OrderPro->item_name = $value['item_name'];
                            $OrderPro->item_image = $value['item_image'];
                            $OrderPro->addons_price = $value['addons_price'];
                            $OrderPro->addons_name = $value['addons_name'];
                            $OrderPro->price = $value['price'];
                            $OrderPro->variation_id = $value['variation_id'];
                            $OrderPro->variation_price = $value['variation_price'];
                            $OrderPro->variation = $value['variation'];
                            $OrderPro->qty = $value['qty'];
                            $OrderPro->item_notes = $value['item_notes'];
                            $OrderPro->addons_id = $value['addons_id'];
                            $OrderPro->save();
                        }
                        $cart=Cart::where('user_id', Session::get('id'))->delete();

                        $count=Cart::where('user_id',Session::get('id'))->count();

                        $wallet = $getuserdata->wallet - $order_total;

                        $UpdateWalletDetails = User::where('id', Session::get('id'))
                        ->update(['wallet' => $wallet]);

                        $Wallet = new Transaction;
                        $Wallet->user_id = Session::get('id');
                        $Wallet->order_id = $order_id;
                        $Wallet->order_number = $order_number;
                        $Wallet->wallet = $order_total;
                        $Wallet->payment_id = NULL;
                        $Wallet->order_type = $request->order_type;
                        $Wallet->transaction_type = '2';
                        $Wallet->save();

                        try{
                            $admintitle = "Order";
                            $adminbody = 'You have received a new order '.$order_number.'';
                            $admingoogle_api_key = $getdata->firebase; 
                            
                            $adminregistrationIds = $getdata->token;
                            #prep the bundle
                            $adminmsg = array
                                (
                                'body'  => $adminbody,
                                'title' => $admintitle,
                                'sound' => 1/*Default sound*/
                                );
                            $adminfields = array
                                (
                                'to'            => $adminregistrationIds,
                                'notification'  => $adminmsg
                                );
                            $adminheaders = array
                                (
                                'Authorization: key=' . $admingoogle_api_key,
                                'Content-Type: application/json'
                                );
                            #Send Reponse To FireBase Server
                            $adminch = curl_init();
                            curl_setopt( $adminch,CURLOPT_URL, 'https://fcm.googleapis.com/fcm/send' );
                            curl_setopt( $adminch,CURLOPT_POST, true );
                            curl_setopt( $adminch,CURLOPT_HTTPHEADER, $adminheaders );
                            curl_setopt( $adminch,CURLOPT_RETURNTRANSFER, true );
                            curl_setopt( $adminch,CURLOPT_SSL_VERIFYPEER, false );
                            curl_setopt( $adminch,CURLOPT_POSTFIELDS, json_encode( $adminfields ) );

                            $result = curl_exec ( $adminch );
                            curl_close ( $adminch );
                        }catch(\Swift_TransportException $e){
                            $response = $e->getMessage() ;
                            return response()->json(['status'=>0,'message'=>trans('messages.notification_error')],200);
                        }

                        try{
                            $getlogo = About::select('logo')->where('id','=','1')->first();
                            $getusers = Order::with('users')->where('order.id', $order_id)->get()->first();
                            $getorders=OrderDetails::with('itemimage')->select('order_details.id','order_details.qty','order_details.price as total_price','item.id','item.item_name','order_details.item_id','order_details.addons_id','order_details.item_notes','order_details.variation_price')
                            ->join('item','order_details.item_id','=','item.id')
                            ->join('order','order_details.order_id','=','order.id')
                            ->where('order_details.order_id',$order_id)->get()->toArray();

                            $arrayName = array();
                            foreach ($getorders as $key => $value) {
                               $arr = explode(',', $value['addons_id']);
                               $arrayName[$key]=$value;
                               $arrayName[$key]['addons']=Addons::whereIn('id',$arr)->get()->toArray();
                            }; 
                            
                            $email=$getuserdata->email;
                            $name=$getuserdata->name;
                            $logo=$getlogo->logo;
                            $currency=$getdata->currency;

                            $data=['getusers'=>$getusers,'getorders'=>$arrayName,'email'=>$email,'name'=>$name,'logo'=>$logo,'currency'=>$currency];
                            Mail::send('Email.emailinvoice',$data,function($message)use($data){
                                $message->from(env('MAIL_USERNAME'))->subject('Order');
                                $message->to($data['email']);
                            } );
                        }catch(\Swift_TransportException $e){
                            $response = $e->getMessage() ;
                            return response()->json(['status'=>0,'message'=>trans('messages.email_error')],200);
                        }
                        
                        Session::put('cart', $count);

                        session()->forget(['offer_amount','offer_code']);
                        
                        return response()->json(['status'=>1,'message'=>trans('messages.order_placed')],200);
                    }
                } else {
                    return response()->json(['status'=>0,'message'=>trans('messages.delivery_unavailable')],200);
                }
            }
            

        } catch (\Exception $e) {
            return  $e->getMessage();
            \Session::put('error',$e->getMessage());
            return redirect()->back();
        }
    }

    public function ordercancel(Request $request)
    {
        $status=Order::select('order.order_total','order.razorpay_payment_id','order.order_type','order.user_id','order.payment_type','order.user_id','order.order_total','order.order_number')
        ->join('users','order.user_id','=','users.id')
        ->where('order.id',$request['order_id'])
        ->get()->first();

        if ($status->payment_type != "0") {
            $walletdata=User::select('wallet')->where('id',$status->user_id)->first();

            $wallet = $walletdata->wallet + $status->order_total;

            $UpdateWalletDetails = User::where('id', $status->user_id)
            ->update(['wallet' => $wallet]);

            $Wallet = new Transaction;
            $Wallet->user_id = Session::get('id');
            $Wallet->order_id = $request->order_id;
            $Wallet->order_number = $status->order_number;
            $Wallet->wallet = $status->order_total;
            $Wallet->payment_id = $status->razorpay_payment_id;
            $Wallet->order_type = $status->order_type;
            $Wallet->transaction_type = '1';
            $Wallet->save();
        }

        $UpdateDetails = Order::where('id', $request->order_id)
                    ->update(['status' => '5']);
        
        if(!empty($UpdateDetails))
        {
            $getalluses=User::select('token','email','firebase')->where('type','1')->first();
            $admintitle = "Order";
            $adminbody = 'Order '.$status->order_number.' has been cancelled by user';
            $admingoogle_api_key = $getalluses->firebase; 
            
            $adminregistrationIds = $getalluses->token;
            #prep the bundle
            $adminmsg = array
                (
                'body'  => $adminbody,
                'title' => $admintitle,
                'sound' => 1/*Default sound*/
                );
            $adminfields = array
                (
                'to'            => $adminregistrationIds,
                'notification'  => $adminmsg
                );
            $adminheaders = array
                (
                'Authorization: key=' . $admingoogle_api_key,
                'Content-Type: application/json'
                );
            #Send Reponse To FireBase Server
            $adminch = curl_init();
            curl_setopt( $adminch,CURLOPT_URL, 'https://fcm.googleapis.com/fcm/send' );
            curl_setopt( $adminch,CURLOPT_POST, true );
            curl_setopt( $adminch,CURLOPT_HTTPHEADER, $adminheaders );
            curl_setopt( $adminch,CURLOPT_RETURNTRANSFER, true );
            curl_setopt( $adminch,CURLOPT_SSL_VERIFYPEER, false );
            curl_setopt( $adminch,CURLOPT_POSTFIELDS, json_encode( $adminfields ) );

            $result = curl_exec ( $adminch );
            curl_close ( $adminch );
            return 1;
        }
        else
        {
            return 1;
        }
    }
}
