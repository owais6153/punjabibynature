<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Cart;
use App\Order;
use App\User;
use App\OrderDetails;
use App\Promocode;
use App\ItemImages;
use App\About;
use App\Addons;
use App\Pincode;
use App\Payment;
use App\Transaction;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Mail;
use Validator;
use Stripe\Stripe;
use Stripe\Customer;
use Stripe\Charge;

class CheckoutController extends Controller
{
    public function summary(Request $request)
    {
        if($request->user_id == ""){
            return response()->json(["status"=>0,"message"=>trans('messages.user_required')],400);
        }

        $cartdata=Cart::select('id','qty','price','item_notes','variation','item_name','tax','addons_price',\DB::raw("CONCAT('".url('/storage/app/public/images/item/')."/', item_image) AS item_image"),'addons_name','item_id','addons_id')
        ->where('is_available','1')
        ->where('user_id',$request->user_id)->get()->toArray();

        foreach ($cartdata as $value) {
            if ($value['addons_name'] == "") {
              $addons_name = "";
            } else {
              $addons_name = $value['addons_name'];
            }

            if ($value['addons_price'] == "") {
              $addons_price = "";
            } else {
              $addons_price = $value['addons_price'];
            }

            if ($value['item_notes'] == "") {
              $item_notes = "";
            } else {
              $item_notes = $value['item_notes'];
            }

            if ($value['addons_id'] == "") {
              $addons_id = "";
            } else {
              $addons_id = $value['addons_id'];
            }

            $data[] = array(
                "id" => $value['id'],
                "qty" => $value['qty'],
                "item_name" => $value['item_name'],
                "variation" => $value['variation'],
                "item_id" => $value['item_id'],
                "item_notes" => $item_notes,
                "addons_name" => $addons_name,
                "addons_price" => $addons_price,
                "addons_id" => $addons_id,
                'item_image' => $value['item_image'],
                "total_price" => $value['qty'] * $value['price'],
                "tax" => number_format(($value['qty']*$value['price'])*$value['tax']/100,2)
            );
        }

        @$order_total = array_sum(array_column(@$data, 'total_price'));
        @$tax = array_sum(array_column(@$data, 'tax'));
        $summery = array(
            'order_total' => "$order_total",
            'tax' => number_format($tax,2),
        );
        
        if(!empty($cartdata))
        {
            return response()->json(['status'=>1,'message'=>'Summery list Successful','data'=>@$data,'summery'=>$summery],200);
        }
        else
        {
            return response()->json(['status'=>0,'message'=>trans('messages.no_data')],200);
        }
    }

    public function order(Request $request)
    {
        if($request->user_id == ""){
            return response()->json(["status"=>0,"message"=>trans('messages.user_required')],400);
        }
        if($request->order_total == ""){
            return response()->json(["status"=>0,"message"=>trans('messages.total_amount')],400);
        }
        
        if($request->payment_type == ""){
            return response()->json(["status"=>0,"message"=>trans('messages.payment_type')],400);
        }

        $order_number = substr(str_shuffle(str_repeat("0123456789ABCDEFGHIJKLMNOPQRSTUVWXYZ", 10)), 0, 10);
        $getdata=User::select('token','firebase','timezone')->where('type','1')->first();
        
        date_default_timezone_set($getdata->timezone);

        $getstripe=Payment::select('environment','test_secret_key','live_secret_key')->where('payment_name','Stripe')->first();

        if ($getstripe->environment == "1") {
            $skey = $getstripe->test_secret_key;
        } else {
            $skey = $getstripe->live_secret_key;
        }

        try {

            if($request->payment_type == "1") {

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

                    if($request->address == ""){
                        return response()->json(["status"=>0,"message"=>trans('messages.address_required')],400);
                    }

                    if($request->lat == ""){
                        return response()->json(["status"=>0,"message"=>trans('messages.select_address')],400);
                    }

                    if($request->lang == ""){
                        return response()->json(["status"=>0,"message"=>trans('messages.select_address')],400);
                    }

                    if($request->pincode == ""){
                        return response()->json(["status"=>0,"message"=>trans('messages.pincode_required')],400);
                    }

                    if($request->building == ""){
                        return response()->json(["status"=>0,"message"=>trans('messages.no_required')],400);
                    }

                    if($request->landmark == ""){
                        return response()->json(["status"=>0,"message"=>trans('messages.landmark_required')],400);
                    }

                    $delivery_charge = $request->delivery_charge;
                    $address = $request->address;
                    $lat = $request->lat;
                    $lang = $request->lang;
                    $order_total = $request->order_total;
                    $building = $request->building;
                    $landmark = $request->landmark;
                    $postal_code = $request->pincode;
                }

                $order = new Order;
                $order->order_number =$order_number;
                $order->user_id =$request->user_id;
                $order->order_total =$order_total;
                $order->razorpay_payment_id =$request->razorpay_payment_id;
                $order->payment_type =$request->payment_type;
                $order->order_type =$request->order_type;
                $order->status ='1';
                $order->address =$address;
                $order->pincode =$postal_code;
                $order->building =$building;
                $order->landmark =$landmark;
                $order->lat =$lat;
                $order->lang =$lang;
                $order->promocode =$request->promocode;
                $order->discount_amount =$request->discount_amount;
                $order->discount_pr =$request->discount_pr;
                $order->tax =$request->tax;
                $order->tax_amount =$request->tax_amount;
                $order->delivery_charge =$delivery_charge;
                $order->order_notes =$request->order_notes;
                $order->order_from =$request->order_from;
                $order->save();

                $order_id = DB::getPdo()->lastInsertId();
                $data=Cart::where('cart.user_id',$request['user_id'])
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
                $cart=Cart::where('user_id', $request->user_id)->delete();


                //Notification
                $getalluses=User::select('token','email','name')->where('id',$request->user_id)
                ->get()->first();

                try{

                    $title = "Order";
                    $body = 'Order "'.$order_number.'" has been placed';
                    $google_api_key = $getdata->firebase; 
                    
                    $registrationIds = $getalluses->token;
                    #prep the bundle
                    $msg = array
                        (
                        'body'  => $body,
                        'title' => $title,
                        'sound' => 1/*Default sound*/
                        );
                    $fields = array
                        (
                        'to'            => $registrationIds,
                        'notification'  => $msg
                        );
                    $headers = array
                        (
                        'Authorization: key=' . $google_api_key,
                        'Content-Type: application/json'
                        );
                    #Send Reponse To FireBase Server
                    $ch = curl_init();
                    curl_setopt( $ch,CURLOPT_URL, 'https://fcm.googleapis.com/fcm/send' );
                    curl_setopt( $ch,CURLOPT_POST, true );
                    curl_setopt( $ch,CURLOPT_HTTPHEADER, $headers );
                    curl_setopt( $ch,CURLOPT_RETURNTRANSFER, true );
                    curl_setopt( $ch,CURLOPT_SSL_VERIFYPEER, false );
                    curl_setopt( $ch,CURLOPT_POSTFIELDS, json_encode( $fields ) );

                    $result = curl_exec ( $ch );
                    curl_close ( $ch );
                }catch(\Swift_TransportException $e){
                    $response = $e->getMessage() ;
                    return response()->json(['status'=>0,'message'=>trans('messages.email_error')],200);
                }

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

                return response()->json(['status'=>1,'message'=>trans('messages.order_placed')],200);

            } elseif ($request->payment_type == "2") {

                $getuserdata=User::select('name')->where('id',$request->user_id)
                ->get()->first();

                if ($request->order_type == "2") {                    
                    $delivery_charge = "0.00";
                    $address = $request->address;
                    $lat = $request->lat;
                    $lang = $request->long;
                    $building = "";
                    $landmark = "";
                    $city = @$request->city;
                    $state = @$request->state;
                    $country = @$request->country;
                    $pincode = $request->pincode;
                    $order_total = $request->order_total-$request->$delivery_charge;
                } else {

                    if($request->address == ""){
                        return response()->json(["status"=>0,"message"=>trans('messages.address_required')],400);
                    }

                    if($request->lat == ""){
                        return response()->json(["status"=>0,"message"=>trans('messages.select_address')],400);
                    }

                    if($request->lang == ""){
                        return response()->json(["status"=>0,"message"=>trans('messages.select_address')],400);
                    }

                    if($request->pincode == ""){
                        return response()->json(["status"=>0,"message"=>trans('messages.pincode_required')],400);
                    }

                    if($request->building == ""){
                        return response()->json(["status"=>0,"message"=>trans('messages.no_required')],400);
                    }

                    if($request->landmark == ""){
                        return response()->json(["status"=>0,"message"=>trans('messages.landmark_required')],400);
                    }

                    $delivery_charge = "0.00";
                    $address = $request->address;
                    $lat = $request->lat;
                    $lang = $request->long;
                    $building = $request->building;
                    $landmark = $request->landmark;
                    $city = @$request->city;
                    $state = @$request->state;
                    $country = @$request->country;
                    $pincode = $request->pincode;
                    $order_total = $request->order_total-$request->$delivery_charge;
                }


                Stripe::setApiKey($skey);

                $customer = Customer::create(array(
                    'email' => $request->stripeEmail,
                    'source' => $request->stripeToken,
                    'name' => $getuserdata->name,
                ));

                $charge = Charge::create(array(
                    'customer' => $customer->id,
                    'amount' => $order_total*100,
                    'currency' => 'usd',
                    'description' => 'Food Service',
                ));

                $order = new Order;
                $order->order_number =$order_number;
                $order->user_id =$request->user_id;
                $order->order_total =$order_total;
                $order->razorpay_payment_id =$charge['id'];
                $order->payment_type =$request->payment_type;
                $order->order_type =$request->order_type;
                $order->status ='1';
                $order->address =$address;
                $order->building =$building;
                $order->landmark =$landmark;
                $order->pincode =$pincode;
                $order->lat =$lat;
                $order->lang =$lang;
                $order->promocode =$request->promocode;
                $order->discount_amount =$request->discount_amount;
                $order->discount_pr =$request->discount_pr;
                $order->tax =$request->tax;
                $order->tax_amount =$request->tax_amount;
                $order->delivery_charge =$delivery_charge;
                $order->order_notes =$request->order_notes;
                $order->order_from =$request->order_from;
                $order->save();

                $order_id = DB::getPdo()->lastInsertId();
                $data=Cart::where('cart.user_id',$request['user_id'])
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
                $cart=Cart::where('user_id', $request->user_id)->delete();


                //Notification
                $getalluses=User::select('token','email','name')->where('id',$request->user_id)
                ->get()->first();

                try{
                    
                    $title = "Order";
                    $body = 'Order "'.$order_number.'" has been placed';
                    $google_api_key = $getdata->firebase; 
                    
                    $registrationIds = $getalluses->token;
                    #prep the bundle
                    $msg = array
                        (
                        'body'  => $body,
                        'title' => $title,
                        'sound' => 1/*Default sound*/
                        );
                    $fields = array
                        (
                        'to'            => $registrationIds,
                        'notification'  => $msg
                        );
                    $headers = array
                        (
                        'Authorization: key=' . $google_api_key,
                        'Content-Type: application/json'
                        );
                    #Send Reponse To FireBase Server
                    $ch = curl_init();
                    curl_setopt( $ch,CURLOPT_URL, 'https://fcm.googleapis.com/fcm/send' );
                    curl_setopt( $ch,CURLOPT_POST, true );
                    curl_setopt( $ch,CURLOPT_HTTPHEADER, $headers );
                    curl_setopt( $ch,CURLOPT_RETURNTRANSFER, true );
                    curl_setopt( $ch,CURLOPT_SSL_VERIFYPEER, false );
                    curl_setopt( $ch,CURLOPT_POSTFIELDS, json_encode( $fields ) );

                    $result = curl_exec ( $ch );
                    // dd($result);
                    curl_close ( $ch );
                }catch(\Swift_TransportException $e){
                    $response = $e->getMessage() ;
                    return response()->json(['status'=>0,'message'=>trans('messages.email_error')],200);
                }

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

                return response()->json(['status'=>1,'message'=>trans('messages.order_placed')],200);
            } elseif ($request->payment_type == "3") {

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

                    if($request->address == ""){
                        return response()->json(["status"=>0,"message"=>trans('messages.address_required')],400);
                    }

                    if($request->lat == ""){
                        return response()->json(["status"=>0,"message"=>trans('messages.select_address')],400);
                    }

                    if($request->lang == ""){
                        return response()->json(["status"=>0,"message"=>trans('messages.select_address')],400);
                    }

                    if($request->pincode == ""){
                        return response()->json(["status"=>0,"message"=>trans('messages.pincode_required')],400);
                    }

                    if($request->building == ""){
                        return response()->json(["status"=>0,"message"=>trans('messages.no_required')],400);
                    }

                    if($request->landmark == ""){
                        return response()->json(["status"=>0,"message"=>trans('messages.landmark_required')],400);
                    }

                    $delivery_charge = $request->delivery_charge;
                    $address = $request->address;
                    $lat = $request->lat;
                    $lang = $request->lang;
                    $order_total = $request->order_total;
                    $building = $request->building;
                    $landmark = $request->landmark;
                    $postal_code = $request->pincode;
                }

                $order = new Order;
                $order->order_number =$order_number;
                $order->user_id =$request->user_id;
                $order->order_total =$order_total;
                $order->payment_type =$request->payment_type;
                $order->order_type =$request->order_type;
                $order->status ='1';
                $order->address =$address;
                $order->building =$building;
                $order->landmark =$landmark;
                $order->pincode =$postal_code;
                $order->lat =$lat;
                $order->lang =$lang;
                $order->promocode =$request->promocode;
                $order->discount_amount =$request->discount_amount;
                $order->discount_pr =$request->discount_pr;
                $order->tax =$request->tax;
                $order->tax_amount =$request->tax_amount;
                $order->delivery_charge =$delivery_charge;
                $order->order_notes =$request->order_notes;
                $order->order_from =$request->order_from;
                $order->save();


                $order_id = DB::getPdo()->lastInsertId();
                $data=Cart::where('cart.user_id',$request['user_id'])
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
                $cart=Cart::where('user_id', $request->user_id)->delete();

                $getalluses=User::select('token','email','name','wallet')->where('id',$request->user_id)
                ->get()->first();

                $wallet = $getalluses->wallet - $order_total;

                $UpdateWalletDetails = User::where('id', $request->user_id)
                ->update(['wallet' => $wallet]);

                $Wallet = new Transaction;
                $Wallet->user_id = $request->user_id;
                $Wallet->order_id = $order_id;
                $Wallet->order_number = $order_number;
                $Wallet->wallet = $order_total;
                $Wallet->payment_id = NULL;
                $Wallet->order_type = $request->order_type;
                $Wallet->transaction_type = '2';
                $Wallet->save();

                //Notification
                

                $title = "Order";
                $body = 'Order "'.$order_number.'" has been placed';
                $google_api_key = $getdata->firebase; 
                
                $registrationIds = $getalluses->token;
                #prep the bundle
                $msg = array
                    (
                    'body'  => $body,
                    'title' => $title,
                    'sound' => 1/*Default sound*/
                    );
                $fields = array
                    (
                    'to'            => $registrationIds,
                    'notification'  => $msg
                    );
                $headers = array
                    (
                    'Authorization: key=' . $google_api_key,
                    'Content-Type: application/json'
                    );
                #Send Reponse To FireBase Server
                $ch = curl_init();
                curl_setopt( $ch,CURLOPT_URL, 'https://fcm.googleapis.com/fcm/send' );
                curl_setopt( $ch,CURLOPT_POST, true );
                curl_setopt( $ch,CURLOPT_HTTPHEADER, $headers );
                curl_setopt( $ch,CURLOPT_RETURNTRANSFER, true );
                curl_setopt( $ch,CURLOPT_SSL_VERIFYPEER, false );
                curl_setopt( $ch,CURLOPT_POSTFIELDS, json_encode( $fields ) );

                $result = curl_exec ( $ch );
                curl_close ( $ch );

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

                return response()->json(['status'=>1,'message'=>trans('messages.order_placed')],200);
                
            } else {
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

                    if($request->address == ""){
                        return response()->json(["status"=>0,"message"=>trans('messages.address_required')],400);
                    }

                    if($request->lat == ""){
                        return response()->json(["status"=>0,"message"=>trans('messages.select_address')],400);
                    }

                    if($request->lang == ""){
                        return response()->json(["status"=>0,"message"=>trans('messages.select_address')],400);
                    }

                    if($request->pincode == ""){
                        return response()->json(["status"=>0,"message"=>trans('messages.pincode_required')],400);
                    }

                    if($request->building == ""){
                        return response()->json(["status"=>0,"message"=>trans('messages.no_required')],400);
                    }

                    if($request->landmark == ""){
                        return response()->json(["status"=>0,"message"=>trans('messages.landmark_required')],400);
                    }

                    $delivery_charge = $request->delivery_charge;
                    $address = $request->address;
                    $lat = $request->lat;
                    $lang = $request->lang;
                    $order_total = $request->order_total;
                    $building = $request->building;
                    $landmark = $request->landmark;
                    $postal_code = $request->pincode;
                }

                $order = new Order;
                $order->order_number =$order_number;
                $order->user_id =$request->user_id;
                $order->order_total =$order_total;
                $order->payment_type =$request->payment_type;
                $order->order_type =$request->order_type;
                $order->status ='1';
                $order->address =$address;
                $order->building =$building;
                $order->landmark =$landmark;
                $order->pincode =$postal_code;
                $order->lat =$lat;
                $order->lang =$lang;
                $order->promocode =$request->promocode;
                $order->discount_amount =$request->discount_amount;
                $order->discount_pr =$request->discount_pr;
                $order->tax =$request->tax;
                $order->tax_amount =$request->tax_amount;
                $order->delivery_charge =$delivery_charge;
                $order->order_notes =$request->order_notes;
                $order->order_from =$request->order_from;
                $order->save();


                $order_id = DB::getPdo()->lastInsertId();
                $data=Cart::where('cart.user_id',$request['user_id'])
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
                $cart=Cart::where('user_id', $request->user_id)->delete();

                //Notification
                $getalluses=User::select('token','email','name')->where('id',$request->user_id)
                ->get()->first();

                try{
                    
                    $title = "Order";
                    $body = 'Order "'.$order_number.'" has been placed';
                    $google_api_key = $getdata->firebase; 
                    
                    $registrationIds = $getalluses->token;
                    #prep the bundle
                    $msg = array
                        (
                        'body'  => $body,
                        'title' => $title,
                        'sound' => 1/*Default sound*/
                        );
                    $fields = array
                        (
                        'to'            => $registrationIds,
                        'notification'  => $msg
                        );
                    $headers = array
                        (
                        'Authorization: key=' . $google_api_key,
                        'Content-Type: application/json'
                        );
                    #Send Reponse To FireBase Server
                    $ch = curl_init();
                    curl_setopt( $ch,CURLOPT_URL, 'https://fcm.googleapis.com/fcm/send' );
                    curl_setopt( $ch,CURLOPT_POST, true );
                    curl_setopt( $ch,CURLOPT_HTTPHEADER, $headers );
                    curl_setopt( $ch,CURLOPT_RETURNTRANSFER, true );
                    curl_setopt( $ch,CURLOPT_SSL_VERIFYPEER, false );
                    curl_setopt( $ch,CURLOPT_POSTFIELDS, json_encode( $fields ) );

                    $result = curl_exec ( $ch );
                    // dd($result);
                    curl_close ( $ch );
                }catch(\Swift_TransportException $e){
                    $response = $e->getMessage() ;
                    return response()->json(['status'=>0,'message'=>trans('messages.email_error')],200);
                }

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

                return response()->json(['status'=>1,'message'=>trans('messages.order_placed')],200);
            }

        } catch (\Exception $e){

            return response()->json(['status'=>0,'message'=>trans('messages.wrong').$e],400);
        }
    }

    public function orderhistory(Request $request)
    {
        if($request->user_id == ""){
            return response()->json(["status"=>0,"message"=>trans('messages.user_required')],400);
        }

        $cartdata=OrderDetails::select('order.order_total as total_price',DB::raw('SUM(order_details.qty) AS qty'),'order.id','order.order_type','order.order_number','order.status','order.payment_type',DB::raw('DATE_FORMAT(order.created_at, "%d-%m-%Y") as date'))
        ->join('item','order_details.item_id','=','item.id')
        ->join('order','order_details.order_id','=','order.id')
        ->where('order.user_id',$request->user_id)->groupBy('order_details.order_id')->orderBy('order_details.order_id', 'DESC')->get();

        if(!empty($cartdata))
        {
            return response()->json(['status'=>1,'message'=>'Order history list Successful','data'=>$cartdata],200);
        }
        else
        {
            return response()->json(['status'=>0,'message'=>trans('messages.no_data')],200);
        }
    }

    public function getorderdetails(Request $request)
    {
        if($request->order_id == ""){
            return response()->json(["status"=>0,"message"=>trans('messages.order_number')],400);
        }

        $cartdata=OrderDetails::with('itemimage')->select('order_details.id','order_details.qty','order_details.price as total_price','item.id','order_details.item_name',\DB::raw("CONCAT('".url('/storage/app/public/images/item/')."/', order_details.item_image) AS item_image"),'order_details.variation','order_details.variation_price','order_details.item_id','order_details.addons_id','order_details.addons_name','order_details.addons_price','order_details.item_notes')
        ->join('item','order_details.item_id','=','item.id')
        ->join('order','order_details.order_id','=','order.id')
        ->where('order_details.order_id',$request->order_id)->get()->toArray();
        
        $status=Order::select('order.driver_id','order.address','order.building','order.landmark','order.pincode','order.promocode','order.discount_amount','order.order_number','order.status','order.order_notes','order.order_type','order.delivery_charge','order.tax_amount','order.order_total')
        ->join('users','order.user_id','=','users.id')
        ->where('order.id',$request['order_id'])
        ->get()->first();

        $getdriver=User::select('users.name',\DB::raw("CONCAT('".url('/storage/app/public/images/profile/')."/', users.profile_image) AS profile_image"),'users.mobile')->where('users.id',$status->driver_id)
        ->get()->first();

        foreach ($cartdata as $value) {

            if ($value['addons_name'] == "") {
              $addons_name = "";
            } else {
              $addons_name = $value['addons_name'];
            }

            if ($value['addons_price'] == "") {
              $addons_price = "";
            } else {
              $addons_price = $value['addons_price'];
            }

            if ($value['item_notes'] == "") {
              $item_notes = "";
            } else {
              $item_notes = $value['item_notes'];
            }

            if ($value['addons_id'] == "") {
              $addons_id = "";
            } else {
              $addons_id = $value['addons_id'];
            }

            $cdata[] = array(
                "id" => $value['id'],
                "qty" => $value['qty'],
                "total_price" => $value['total_price'],
                "item_name" => $value['item_name'],
                "item_id" => $value['item_id'],
                "item_notes" => $item_notes,
                "addons_name" => $addons_name,
                "addons_price" => $addons_price,
                "addons_id" => $addons_id,
                'variation' => $value['variation'],
                'item_image' => $value['item_image'],
                "order_total" => $value['qty'] * $value['total_price'],
            );
        }

        @$order_total = array_sum(array_column(@$cdata, 'order_total'));

        $summery = array(
            'order_total' => $order_total,
            'tax' => $status->tax_amount,
            'discount_amount' => $status->discount_amount,
            'promocode' => $status->promocode,
            'order_notes' => $status->order_notes,
            'delivery_charge' => $status->delivery_charge,
            "driver_name" => @$getdriver["name"],
            "driver_profile_image" => @$getdriver["profile_image"],
            "driver_mobile" => @$getdriver["mobile"],
        );
        
        if(!empty($cartdata))
        {
            return response()->json(['status'=>1,'message'=>'Summery list Successful','address'=>$status->address,'landmark' => $status->landmark,'building' => $status->building,'pincode'=>$status->pincode,'order_number'=>$status->order_number,'order_type'=>$status->order_type,'data'=>@$cdata,'summery'=>$summery],200);
        }
        else
        {
            return response()->json(['status'=>0,'message'=>trans('messages.no_data')],200);
        }
    }

    public function ordercancel(Request $request)
    {
        if($request->order_id == ""){
            return response()->json(["status"=>0,"message"=>trans('messages.order_number')],400);
        }

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
            $Wallet->user_id = $status->user_id;
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

        $getdata=User::select('token','firebase')->where('type','1')->first();
        $admintitle = "Order";
        $adminbody = 'Order '.$status->order_number.' has been cancelled by user';
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
        
        if(!empty($UpdateDetails))
        {
            return response()->json(['status'=>1,'message'=>trans('messages.order_cancel')],200);
        }
        else
        {
            return response()->json(['status'=>0,'message'=>trans('messages.wrong')],400);
        }
    }

    public function wallet(Request $request)
    {
        if($request->user_id == ""){
            return response()->json(["status"=>0,"message"=>trans('messages.user_required')],400);
        }

        $walletamount=User::select('wallet')->where('id',$request->user_id)->first();

        $transaction_data=Transaction::select('order_number','transaction_type','order_type','wallet',DB::raw('DATE_FORMAT(created_at, "%d-%m-%Y") as date'),'username')->where('user_id',$request->user_id)->orderBy('id', 'DESC')->get();

        if(!empty($transaction_data))
        {
            return response()->json(['status'=>1,'message'=>'Transaction list Successful','walletamount'=>$walletamount->wallet,'data'=>$transaction_data],200);
        }   
        else
        {
            return response()->json(['status'=>0,'message'=>trans('messages.no_data')],200);
        }
    }

    public function paymenttype(Request $request)
    {
        if($request->user_id == ""){
            return response()->json(["status"=>0,"message"=>trans('messages.user_required')],400);
        }

        $getdata=Payment::select('payment_name','test_public_key','live_public_key','environment')->where('is_available','1')->orderBy('id', 'DESC')->get();

        $getlogo = About::select(\DB::raw("CONCAT('".url('/storage/app/public/images/about/')."/', logo) AS logo"))->where('id','=','1')->first();

        $walletamount=User::select('wallet')->where('id',$request->user_id)->first();

        if(!empty($getdata))
        {
            return response()->json(['status'=>1,'message'=>'Transaction list Successful','walletamount'=>$walletamount->wallet,'payment'=>$getdata,'logo'=>$getlogo->logo],200);
        }
        else
        {
            return response()->json(['status'=>0,'message'=>trans('messages.no_data')],200);
        }
    }

    public function promocodelist()
    {
        
        $promocode=Promocode::select('promocode.offer_name','promocode.offer_code','promocode.offer_amount','promocode.description')
        ->where('is_available','=','1')
        ->get();

        if(!empty($promocode))
        {
            return response()->json(['status'=>1,'message'=>'Promocode List','data'=>$promocode],200);
        }
        else
        {
            return response()->json(['status'=>0,'message'=>trans('messages.no_data')],200);
        }
    }

    public function promocode(Request $request)
    {
        if($request->offer_code == ""){
            return response()->json(["status"=>0,"message"=>trans('messages.promocode')],400);
        }

        if($request->user_id == ""){
            return response()->json(["status"=>0,"message"=>trans('messages.user_required')],400);
        }

        $checkpromo=Order::select('promocode')->where('promocode',$request->offer_code)->where('user_id',$request->user_id)
        ->count();

        if ($checkpromo > "0" ) {
            return response()->json(['status'=>0,'message'=>trans('messages.once_per_user')],200);
        } else {
            $promocode=Promocode::select('promocode.offer_amount','promocode.description','promocode.offer_code')->where('promocode.offer_code',$request['offer_code'])
            ->get()->first();

            if($promocode['offer_code']== $request->offer_code) {
                if(!empty($promocode))
                {
                    return response()->json(['status'=>1,'message'=>'Promocode has been applied','data'=>$promocode],200);
                }
            } else {
                return response()->json(['status'=>0,'message'=>trans('messages.wrong_promocode')],200);
            }
        }
    }

    public function checkpincode(Request $request)
    {
        if($request->pincode == ""){
            return response()->json(["status"=>0,"message"=>trans('messages.pincode_required')],400);
        }

        $pincode=Pincode::select('pincode.pincode')->where('pincode.pincode',$request['pincode'])
        ->get()->first();

        if(@$pincode['pincode'] == $request->pincode) {
            if(!empty($pincode))
            {
                return response()->json(['status'=>1,'message'=>'Pincode is available for delivery'],200);
            }
        } else {
            return response()->json(['status'=>0,'message'=>trans('messages.delivery_unavailable')],200);
        }
    }

    public function addmoney(Request $request)
    {
        if($request->user_id == ""){
            return response()->json(["status"=>0,"message"=>trans('messages.user_required')],400);
        }
        if($request->amount == ""){
            return response()->json(["status"=>0,"message"=>trans('messages.enter_amount')],400);
        }
        if($request->order_type == ""){
            return response()->json(["status"=>0,"message"=>trans('messages.order_type')],400);
        }

        if ($request->order_type == "3") {
            try {
                $getuserdata=User::where('id',$request->user_id)
                ->get()->first(); 

                $wallet = $getuserdata->wallet + $request->amount;

                $UpdateWalletDetails = User::where('id', $request->user_id)
                ->update(['wallet' => $wallet]);

                if ($UpdateWalletDetails) {
                    $order_number = substr(str_shuffle(str_repeat("0123456789ABCDEFGHIJKLMNOPQRSTUVWXYZ", 10)), 0, 10);

                    $Wallet = new Transaction;
                    $Wallet->user_id = $request->user_id;
                    $Wallet->order_id = NULL;
                    $Wallet->order_number = $order_number;
                    $Wallet->wallet = $request->amount;
                    $Wallet->payment_id = $request->payment_id;
                    $Wallet->order_type = $request->order_type;
                    $Wallet->transaction_type = '4';
                    $Wallet->save();

                    return response()->json(['status'=>1,'message'=>trans('messages.money_added')],200);
                } else {
                    return response()->json(['status'=>0,'message'=>trans('messages.money_error')],200);
                }                    

            } catch (\Exception $e) {
                return response()->json(['status'=>1,'message'=>$e],200);
            }
        }

        if ($request->order_type == "4") {
            try {
                $getuserdata=User::where('id',$request->user_id)
                ->get()->first();

                $getpaymentdata=Payment::select('test_secret_key','live_secret_key','environment')->where('payment_name','Stripe')->first();

                if ($getpaymentdata->environment=='1') {
                    $stripe_secret = $getpaymentdata->test_secret_key;
                } else {
                    $stripe_secret = $getpaymentdata->live_secret_key;
                }

                Stripe::setApiKey($stripe_secret);

                $customer = Customer::create(array(
                    'email' => $getuserdata->email,
                    'source' => $request->stripeToken,
                    'name' => $getuserdata->name,
                ));

                $charge = Charge::create(array(
                    'customer' => $customer->id,
                    'amount' => $request->amount*100,
                    'currency' => 'usd',
                    'description' => 'Add Money to wallet',
                ));

                $wallet = $getuserdata->wallet + $request->amount;

                $UpdateWalletDetails = User::where('id', $request->user_id)
                ->update(['wallet' => $wallet]);

                if ($UpdateWalletDetails) {
                    $order_number = substr(str_shuffle(str_repeat("0123456789ABCDEFGHIJKLMNOPQRSTUVWXYZ", 10)), 0, 10);

                    $Wallet = new Transaction;
                    $Wallet->user_id = $request->user_id;
                    $Wallet->order_id = NULL;
                    $Wallet->order_number = $order_number;
                    $Wallet->wallet = $request->amount;
                    $Wallet->payment_id = $charge['id'];
                    $Wallet->order_type = $request->order_type;
                    $Wallet->transaction_type = '4';
                    $Wallet->save();

                    return response()->json(['status'=>1,'message'=>trans('messages.money_added')],200); 
                } else {
                    return response()->json(['status'=>0,'message'=>trans('messages.money_error')],200);
                }
            } catch (Exception $e) {
                return response()->json(['status'=>0,'message'=>$e],200);
            }
        }
    }
}
