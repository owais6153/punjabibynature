<?php

namespace App\Http\Controllers\front;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Redirect;
use Session;
use Auth;
use App\User;
use App\Ratting;
use App\OTPConfiguration;
use App\About;
use App\Transaction;
use App\Cart;
use App\Address;
use App\Pincode;
use App\Payment;
use Validator;
use Twilio\Rest\Client;
use App\Item;
use App\ItemImages;


class UserotpController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function verifyotp_checkout(Request $request){
        if($request->code == ""){
            return response()->json(["status"=>0,"message"=>"Code Required"],400);
        }
        $checkmobile=User::where('otp',$request->code)->first();
        if (empty($checkmobile)) {
            return response()->json(["status"=>0,"message"=>"Wrong Code"],400);
        }

        if ($checkmobile->mobile != $request->mobile) {
            return response()->json(["status"=>0,"message"=>"Wrong Number", 'num' => $checkmobile->mobile, 'verify_num' =>  $request->mobile],400);
        }


        if ($checkmobile->otp == $request->code) {
            $update=User::where('mobile',$request->mobile)->update(['otp'=>NULL,'is_verified'=>'1']);

            if ($checkmobile->login_type == 'email') {
                 $cart=Cart::where('user_id',$checkmobile->id)->count();
                 session ( [ 
                    'id' => $checkmobile->id, 
                    'name' => $checkmobile->name,
                    'email' => $checkmobile->email,
                    'mobile' => $checkmobile->mobile,
                    'referral_code' => $checkmobile->referral_code,
                    'profile_image' => 'unknown.png',
                    'cart' => $cart,
                ] );


                if (Session::get('guest_cart')) {
                    $cartdata_temp = Session::get('guest_cart');
                    $cartdata = json_decode(json_encode($cartdata_temp)); 



                    foreach ($cartdata as $key => $value) {
                        $getitem=Item::with('itemimage')->select('item.id','item.item_name','item.tax')
                        ->where('item.id',$value->item_id)->first();
                        $cart = new Cart;
                        $cart->item_id =$value->item_id;
                        $cart->addons_id =$value->addons_id;
                        $cart->qty =$value->qty;
                        $cart->price =$value->price;
                        $cart->variation_id =$value->variation_id;
                        $cart->variation_price =$value->variation_price;
                        $cart->variation =$value->variation;
                        $cart->user_id =$checkmobile->id;
                        $cart->item_notes =$value->item_notes;
                        $cart->item_name =$getitem->item_name;
                        $cart->tax =$getitem->tax;
                        $cart->item_image =$getitem['itemimage']->image_name;
                        $cart->addons_name =$value->addons_name;
                        $cart->addons_price =$value->addons_price;
                        $cart->product_type =$value->product_type;
                        $cart->food_type = $request->food_type;
                        $cart->catering_cat = $request->catering_cat;

                        $cart->ingredients =  (isset($value->ingredients) && !empty($value->ingredients)) ? implode('|',$value->ingredients) : null;
                        $cart->combo = (isset($value->combo) && !empty($value->combo)) ? implode('|',$value->combo) : null;
                        $cart->group_addons = (isset($value->group_addons) && !empty($value->group_addons)) ? implode('|',$value->group_addons) : null;
                        $cart->totalAddonPrice =$value->totalAddonPrice;

                        $cart->save();


                    }
                    Session::forget('guest_cart');
                    $count=Cart::where('user_id',$checkmobile->id)->count();

                    Session::put('cart', $count);
        
                }

            }
            

            return response()->json(['status'=>1,'message'=>'Verified',],200);

        }

    }
    public function resendcode_checkout(Request $request){
        $otp = rand ( 100000 , 999999 );
        if($request->mobile == ""){
            return response()->json(["status"=>0,"message"=>trans('messages.email_required')],400);
        }
            $getconfiguration = OTPConfiguration::where('status','1')->first();
          if ($getconfiguration->name == "twilio") {
              $sid    = $getconfiguration->twilio_sid; 
              $token  = $getconfiguration->twilio_auth_token; 
              $twilio = new Client($sid, $token); 
              
              $message = $twilio->messages 
                          ->create($request->mobile, // to 
                  array( 
                      "from" => $getconfiguration->twilio_mobile_number,
                      "body" => "Your Login Code for Punjabi By Nature is ".$otp 
                 ) 
              );
              $otp_sent = true;
          }

          if ($getconfiguration->name == "msg91") {
              $curl = curl_init();

              curl_setopt_array($curl, array(
                  CURLOPT_URL => "https://api.msg91.com/api/v5/otp?template_id=".$getconfiguration->msg_template_id."&mobile=".$request->mobile."&authkey=".$getconfiguration->msg_authkey."",
                  CURLOPT_RETURNTRANSFER => true,
                  CURLOPT_ENCODING => "",
                  CURLOPT_MAXREDIRS => 10,
                  CURLOPT_TIMEOUT => 30,
                  CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
                  CURLOPT_CUSTOMREQUEST => "GET",
                  CURLOPT_HTTPHEADER => array(
                      "content-type: application/json"
                  ),
              ));

              $response = curl_exec($curl);
              $otp_sent = true;
              $err = curl_error($curl);

              curl_close($curl);
          }
          $update=User::where('mobile',$request->mobile)->update(['otp'=>$otp]);
          return response()->json(['status'=>1,'message'=>'Code resent'],200);


    }
    public function register_on_checkout(Request $request){
        $checkemail=User::where('email',$request->email)->first();
        $checkmobile=User::where('mobile',$request->mobile)->first();
        $str_result = 'ABCDEFGHIJKLMNOPQRSTUVWXYZ1234567890abcdefghijklmnopqrstuvwxyz'; 
        $referral_code = substr(str_shuffle($str_result), 0, 10); 
        $otp = rand ( 100000 , 999999 );
        
       if($request->email == ""){
            return response()->json(["status"=>0,"message"=>trans('messages.email_required')],400);
        }
        if($request->name == ""){
            return response()->json(["status"=>0,"message"=>trans('messages.full_name_required')],400);
        }
        if($request->mobile == ""){
            return response()->json(["status"=>0,"message"=>trans('messages.mobile_required')],400);
        }
        if ($request->order_type == 1) {
            if ($request->city == "") {
               return response()->json(["status"=>0,"message"=>'City is required'],400);
            }
            if ($request->state == "") {
                return response()->json(["status"=>0,"message"=>'State is required'],400);
            }

            if ($request->country == "") {
                return response()->json(["status"=>0,"message"=>'Country is required'],400);
            }
        }

        if(!empty($checkemail) && $checkemail->login_type != 'guest')
        {
           if ($request->register_type == 2) {     
            return response()->json(['status'=>0,'message'=>trans('messages.email_exist') . '. Please Login or use diffrent email'],400);
          }
           if ($request->register_type == 1) {
            return response()->json(['status'=>0,'message'=>'Email already exists. Please login first.'],400);
           }     
        }

        if($request->register_type == 2 && $request->confirm_password != $request->password)
        {
            return response()->json(['status'=>0,'message'=> 'Password not matched'],400);
        }

        if(!empty($checkmobile) && $checkmobile->login_type != 'guest')
        {
            return response()->json(['status'=>0,'message'=>trans('messages.mobile_exist') . '. Please Login or use diffrent number'],400);
        }
        $otp_verified = $otp_sent = false;
        if (!empty($checkmobile) && $checkmobile->login_type == 'guest' && $checkmobile->is_verified == 1) {
          $otp_verified = true;
        }
        else{
          $getconfiguration = OTPConfiguration::where('status','1')->first();
          if ($getconfiguration->name == "twilio") {
              $sid    = $getconfiguration->twilio_sid; 
              $token  = $getconfiguration->twilio_auth_token; 
              $twilio = new Client($sid, $token); 
              
              $message = $twilio->messages 
                          ->create($request->mobile, // to 
                  array( 
                      "from" => $getconfiguration->twilio_mobile_number,
                      "body" => "Your Login Code for Punjabi By Nature is ".$otp 
                 ) 
              );
              $otp_sent = true;
          }

          if ($getconfiguration->name == "msg91") {
              $curl = curl_init();

              curl_setopt_array($curl, array(
                  CURLOPT_URL => "https://api.msg91.com/api/v5/otp?template_id=".$getconfiguration->msg_template_id."&mobile=".$request->mobile."&authkey=".$getconfiguration->msg_authkey."",
                  CURLOPT_RETURNTRANSFER => true,
                  CURLOPT_ENCODING => "",
                  CURLOPT_MAXREDIRS => 10,
                  CURLOPT_TIMEOUT => 30,
                  CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
                  CURLOPT_CUSTOMREQUEST => "GET",
                  CURLOPT_HTTPHEADER => array(
                      "content-type: application/json"
                  ),
              ));

              $response = curl_exec($curl);
              $otp_sent = true;
              $err = curl_error($curl);

              curl_close($curl);
          }
        }
        $password = (isset($request->password) ) ? Hash::make($request->get('password')) : 'password';
        $data['name']=$request->get('name');
        $data['mobile']=$request->get('mobile');
        $data['email']=$request->get('email');
        $data['profile_image']='unknown.png';
        $data['password']= $password;
        $data['referral_code']= $referral_code;
        // $data['token'] = $request->get('token');
        $data['login_type']= ($request->register_type == 2) ? 'email' : 'guest';
        // $data['google_id']=$request->get('google_id');
        // $data['facebook_id']=$request->get('facebook_id');
        $data['otp']=$otp;
        $data['type']='2';



        if (empty($checkmobile) ) {
            $user=User::create($data);
        }
        else{
            $user=User::where('mobile', $request->mobile)->first();
            if ($user->login_type == "guest" && $data['login_type'] == 'email') {
                $update=User::where('mobile',$request->mobile)->update(['login_type'=>'email']);
                 $cart=Cart::where('user_id',$user->id)->count();
                 session ( [ 
                    'id' => $user->id, 
                    'name' => $user->name,
                    'email' => $user->email,
                    'mobile' => $user->mobile,
                    'referral_code' => $user->referral_code,
                    'profile_image' => 'unknown.png',
                    'cart' => $cart,
                ] );
            }

        }
         $arrayName = array(
            'id' => $user->id,
            'name' => $user->name,
            'mobile' => $user->mobile,
            'email' => $user->email,
            'login_type' => $user->login_type,
            'referral_code' => $user->referral_code,
            'profile_image' => url('/storage/app/public/images/profile/'.$user->profile_image),
            'otp_status' => $otp_verified,
            'otp_sent' => $otp_sent,
        );

        if ($data['login_type'] == 'email' ) {
            if ($request->city == "" && $request->state == "" && $request->country == "") {
                $address = new Address;
                $address->user_id =  $user->id;
                $address->address_type = $request->address_type;
                $address->address = $request->address;
                $address->lat = $request->lat;
                $address->lang = $request->lang;
                $address->city = $request->city;
                $address->state = $request->state;
                $address->country = $request->country;
                $address->landmark = $request->landmark;
                $address->building = $request->building;            
                $address->delivery_charge = 0;
                $address->save();
            }            
        }

           
        if (isset($user->id)) {

          return response()->json(['status'=>1,'message'=>'Registration Successful','data'=>$arrayName],200);
        }
        else{
          return response()->json(['status'=>0,'message'=> 'Error in creating user in DB'],400);
        }
        




    }
    public function index() {
        $getabout = About::where('id','=','1')->first();
        Session::forget('facebook_id');
        Session::forget('google_id');
        return view('front.login', compact('getabout'));
    }

    public function signup() {
        $getabout = About::where('id','=','1')->first();
        return view('front.signup', compact('getabout'));
    }


    public function login(Request $request)
    {
        $login=User::where('mobile','+'.$request->country.''.$request->mobile)->where('type','=','2')->first();

        $getdata=User::select('referral_amount')->where('type','1')->first();
        $otp = rand ( 100000 , 999999 );
        if(!empty($login)) {
            if($login->is_available == '1') {
                // Check item in Cart
                $cart=Cart::where('user_id',$login->id)->count();

                session ( [ 
                    'id' => $login->id, 
                    'name' => $login->name,
                    'email' => $login->email,
                    'mobile' => $login->mobile,
                    'profile_image' => $login->profile_image,
                    'referral_code' => $login->referral_code,
                    'referral_amount' => $getdata->referral_amount,
                    'cart' => $cart,
                ] );

                $getconfiguration = OTPConfiguration::where('status','1')->first();
                if ($getconfiguration->name == "twilio") {
                    $sid    = $getconfiguration->twilio_sid; 
                    $token  = $getconfiguration->twilio_auth_token; 
                    $twilio = new Client($sid, $token); 
                    
                    $message = $twilio->messages 
                                ->create('+'.$request->country.''.$request->mobile, // to 
                        array( 
                            "from" => $getconfiguration->twilio_mobile_number,
                            "body" => "Your Login Code ".$otp 
                       ) 
                    );
                }

                if ($getconfiguration->name == "msg91") {
                    $curl = curl_init();

                    curl_setopt_array($curl, array(
                        CURLOPT_URL => "https://api.msg91.com/api/v5/otp?template_id=".$getconfiguration->msg_template_id."&mobile=".'+'.$request->country.''.$request->mobile."&authkey=".$getconfiguration->msg_authkey."",
                        CURLOPT_RETURNTRANSFER => true,
                        CURLOPT_ENCODING => "",
                        CURLOPT_MAXREDIRS => 10,
                        CURLOPT_TIMEOUT => 30,
                        CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
                        CURLOPT_CUSTOMREQUEST => "GET",
                        CURLOPT_HTTPHEADER => array(
                            "content-type: application/json"
                        ),
                    ));

                    $response = curl_exec($curl);
                    $err = curl_error($curl);

                    curl_close($curl);

                    $res = json_decode($response);

                    if ($res->type == "error") {
                        return Redirect::back()->with('danger', $res->message);
                    }
                }

                if (env('Environment') == 'sendbox') {
                    session ( [
                        'mobile' => '+'.$request->country.''.$request->mobile,
                        'password' => $login->password,
                        'otp' => $otp,
                    ] );
                } else {
                    session ( [
                        'mobile' => '+'.$request->country.''.$request->mobile,
                        'password' => $login->password,
                    ] );
                }

                $data_token['otp'] = $otp;
                $update=User::where('mobile',$login->mobile)->update($data_token);

                return Redirect::to('/otp-verify')->with('success', trans('messages.email_sent'));
            } else {
                return Redirect::back()->with('danger', trans('messages.blocked'));
            }
        } else {
            return Redirect::back()->with('danger', trans('messages.invalid_mobile'));
        }    
    }

    public function register(Request $request)
    {
        if (Session::get('facebook_id') OR Session::get('google_id')) {
            $validation = Validator::make($request->all(),[
                'name' => 'required',
                'email' => 'required',
                'mobile' => 'required',
                'accept' =>'accepted'
            ]);
            if ($validation->fails())
            {
                return Redirect::back()->withErrors($validation, 'login')->withInput();
            }
            else
            {
                $str_result = 'ABCDEFGHIJKLMNOPQRSTUVWXYZ1234567890abcdefghijklmnopqrstuvwxyz'; 
                $referral_code = substr(str_shuffle($str_result), 0, 10); 
                $otp = rand ( 100000 , 999999 );

                $checkreferral=User::select('id','name','referral_code','wallet')->where('referral_code',$request['referral_code'])->first();

                if (@$checkreferral->referral_code == $request['referral_code']) {

                    $users=User::where('mobile','+'.$request->country.''.$request->mobile)->get()->first();
                    try{
                        
                        $res = new User;
                        $res->name =$request->name;
                        $res->email =$request->email;
                        $res->mobile ='+'.$request->country.''.$request->mobile;
                        $res->profile_image ='unknown.png';

                        if (Session::get('facebook_id') != "") {
                            $res->login_type ='facebook';
                            $res->facebook_id =Session::get('facebook_id');
                        } else {
                            $res->login_type ='google';
                            $res->google_id =Session::get('google_id');
                        }
                        $res->type ='2';
                        $res->otp =$otp;
                        $res->referral_code =$referral_code;
                        $res->save();

                        $getconfiguration = OTPConfiguration::where('status','1')->first();
                        if ($getconfiguration->name == "twilio") {
                            $sid    = $getconfiguration->twilio_sid; 
                            $token  = $getconfiguration->twilio_auth_token; 
                            $twilio = new Client($sid, $token); 
                            
                            $message = $twilio->messages 
                                        ->create('+'.$request->country.''.$request->mobile, // to 
                                array( 
                                    "from" => $getconfiguration->twilio_mobile_number,
                                    "body" => "Your Login Code ".$otp 
                               ) 
                            );
                        }

                        if ($getconfiguration->name == "msg91") {
                            $curl = curl_init();

                            curl_setopt_array($curl, array(
                                CURLOPT_URL => "https://api.msg91.com/api/v5/otp?template_id=".$getconfiguration->msg_template_id."&mobile=".'+'.$request->country.''.$request->mobile."&authkey=".$getconfiguration->msg_authkey."",
                                CURLOPT_RETURNTRANSFER => true,
                                CURLOPT_ENCODING => "",
                                CURLOPT_MAXREDIRS => 10,
                                CURLOPT_TIMEOUT => 30,
                                CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
                                CURLOPT_CUSTOMREQUEST => "GET",
                                CURLOPT_HTTPHEADER => array(
                                    "content-type: application/json"
                                ),
                            ));

                            $response = curl_exec($curl);
                            $err = curl_error($curl);

                            curl_close($curl);

                            $res = json_decode($response);

                            if ($res->type == "error") {
                                return Redirect::back()->with('danger', $res->message);
                            }
                        }

                        if ($request['referral_code'] != "") {
                            $getdata=User::select('referral_amount')->where('type','1')->get()->first();

                            $wallet = $checkreferral->wallet + $getdata->referral_amount;

                            if ($wallet) {
                                $UpdateWalletDetails = User::where('id', $checkreferral->id)
                                ->update(['wallet' => $wallet]);

                                $from_Wallet = new Transaction;
                                $from_Wallet->user_id = $checkreferral->id;
                                $from_Wallet->order_id = null;
                                $from_Wallet->order_number = null;
                                $from_Wallet->wallet = $getdata->referral_amount;
                                $from_Wallet->payment_id = null;
                                $from_Wallet->order_type = '0';
                                $from_Wallet->transaction_type = '3';
                                $from_Wallet->username = $request->name;
                                $from_Wallet->save();
                            }

                            if ($getdata->referral_amount) {
                                $UpdateWallet = User::where('id', $users->id)
                                ->update(['wallet' => $getdata->referral_amount]);

                                $to_Wallet = new Transaction;
                                $to_Wallet->user_id = $users->id;
                                $to_Wallet->order_id = null;
                                $to_Wallet->order_number = null;
                                $to_Wallet->wallet = $getdata->referral_amount;
                                $to_Wallet->payment_id = null;
                                $to_Wallet->order_type = '0';
                                $to_Wallet->transaction_type = '3';
                                $to_Wallet->username = $checkreferral->name;
                                $to_Wallet->save();
                            }
                        }

                        if (env('Environment') == 'sendbox') {
                            session ( [
                                'mobile' => '+'.$request->country.''.$request->mobile,
                                'otp' => $otp,
                            ] );
                        } else {
                            session ( [
                                'mobile' => '+'.$request->country.''.$request->mobile,
                            ] );
                        }

                        if (Session::get('guest_cart')) {
                            $cartdata_temp = Session::get('guest_cart');
                            $cartdata = json_decode(json_encode($cartdata_temp)); 



                            foreach ($cartdata as $key => $value) {
                                $getitem=Item::with('itemimage')->select('item.id','item.item_name','item.tax')
                                ->where('item.id',$value->item_id)->first();
                                $cart = new Cart;
                                $cart->item_id =$value->item_id;
                                $cart->addons_id =$value->addons_id;
                                $cart->qty =$value->qty;
                                $cart->price =$value->price;
                                $cart->variation_id =$value->variation_id;
                                $cart->variation_price =$value->variation_price;
                                $cart->variation =$value->variation;
                                $cart->user_id =$user->id;
                                $cart->item_notes =$value->item_notes;
                                $cart->item_name =$getitem->item_name;
                                $cart->tax =$getitem->tax;
                                $cart->item_image =$getitem['itemimage']->image_name;
                                $cart->addons_name =$value->addons_name;
                                $cart->addons_price =$value->addons_price;
                                $cart->product_type =$value->product_type;
                                $cart->food_type = $request->food_type;
                                $cart->catering_cat = $request->catering_cat;

                                $cart->ingredients =  (isset($value->ingredients) && !empty($value->ingredients)) ? implode('|',$value->ingredients) : null;
                                $cart->combo = (isset($value->combo) && !empty($value->combo)) ? implode('|',$value->combo) : null;
                                $cart->group_addons = (isset($value->group_addons) && !empty($value->group_addons)) ? implode('|',$value->group_addons) : null;
                                $cart->totalAddonPrice =$value->totalAddonPrice;

                                $cart->save();


                            }
                            Session::forget('guest_cart');
                            $count=Cart::where('user_id',$user->id)->count();

                            Session::put('cart', $count);
                
                        }

                        return Redirect::to('/otp-verify')->with('success', trans('messages.email_sent'));  
                    }catch(\Swift_TransportException $e){
                        $response = $e->getMessage() ;
                        return Redirect::back()->with('danger', trans('messages.email_error'));
                    } 
                } else {
                    return redirect()->back()->with('danger', trans('messages.invalid_referral_code'));
                }
            }
            return Redirect::back()->withErrors(['msg', trans('messages.wrong')]);
        } else {
            $validation = Validator::make($request->all(),[
                'name' => 'required',
                'email' => 'required|unique:users',
                'mobile' => 'required|unique:users',
                'accept' =>'accepted'
            ]);
            
            if ($validation->fails())
            {
                return Redirect::back()->withErrors($validation, 'login')->withInput();
            }
            else
            {
                $str_result = 'ABCDEFGHIJKLMNOPQRSTUVWXYZ1234567890abcdefghijklmnopqrstuvwxyz'; 
                $referral_code = substr(str_shuffle($str_result), 0, 10); 
                $otp = rand ( 100000 , 999999 );

                $checkreferral=User::select('id','name','referral_code','wallet')->where('referral_code',$request['referral_code'])->first();

                if (@$checkreferral->referral_code == $request['referral_code']) {

                    try{

                        $getconfiguration = OTPConfiguration::where('status','1')->first();
                        if ($getconfiguration->name == "twilio") {
                            $sid    = $getconfiguration->twilio_sid; 
                            $token  = $getconfiguration->twilio_auth_token; 
                            $twilio = new Client($sid, $token); 
                            
                            $message = $twilio->messages 
                                        ->create('+'.$request->country.''.$request->mobile, // to 
                                array( 
                                    "from" => $getconfiguration->twilio_mobile_number,
                                    "body" => "Your Login Code ".$otp 
                               ) 
                            );
                        }

                        if ($getconfiguration->name == "msg91") {
                            $curl = curl_init();

                            curl_setopt_array($curl, array(
                                CURLOPT_URL => "https://api.msg91.com/api/v5/otp?template_id=".$getconfiguration->msg_template_id."&mobile=".'+'.$request->country.''.$request->mobile."&authkey=".$getconfiguration->msg_authkey."",
                                CURLOPT_RETURNTRANSFER => true,
                                CURLOPT_ENCODING => "",
                                CURLOPT_MAXREDIRS => 10,
                                CURLOPT_TIMEOUT => 30,
                                CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
                                CURLOPT_CUSTOMREQUEST => "GET",
                                CURLOPT_HTTPHEADER => array(
                                    "content-type: application/json"
                                ),
                            ));

                            $response = curl_exec($curl);
                            $err = curl_error($curl);

                            curl_close($curl);

                            $res = json_decode($response);

                            if ($res->type == "error") {
                                return Redirect::back()->with('danger', $res->message);
                            }
                        }

                        $user = new User;
                        $user->name =$request->name;
                        $user->email =$request->email;
                        $user->mobile ='+'.$request->country.''.$request->mobile;
                        $user->profile_image ='unknown.png';
                        $user->login_type ='email';
                        $user->otp=$otp;
                        $user->type ='2';
                        $user->referral_code=$referral_code;
                        $user->password =Hash::make($request->password);
                        $user->save();


                        if (Session::get('guest_cart')) {
                            $cartdata_temp = Session::get('guest_cart');
                            $cartdata = json_decode(json_encode($cartdata_temp)); 
                            foreach ($cartdata as $key => $value) {
                                $getitem=Item::with('itemimage')->select('item.id','item.item_name','item.tax')
                                ->where('item.id',$value->item_id)->first();
                                $cart = new Cart;
                                $cart->item_id =$value->item_id;
                                $cart->addons_id =$value->addons_id;
                                $cart->qty =$value->qty;
                                $cart->price =$value->price;
                                $cart->variation_id =$value->variation_id;
                                $cart->variation_price =$value->variation_price;
                                $cart->variation =$value->variation;
                                $cart->user_id =$user->id;
                                $cart->item_notes =$value->item_notes;
                                $cart->item_name =$getitem->item_name;
                                $cart->tax =$getitem->tax;
                                $cart->item_image =$getitem['itemimage']->image_name;
                                $cart->addons_name =$value->addons_name;
                                $cart->addons_price =$value->addons_price;
                                $cart->product_type =$value->product_type;
                                $cart->food_type = $request->food_type;
                                $cart->catering_cat = $request->catering_cat;

                                $cart->ingredients =  (isset($value->ingredients) && !empty($value->ingredients)) ? implode('|',$value->ingredients) : null;
                                $cart->combo = (isset($value->combo) && !empty($value->combo)) ? implode('|',$value->combo) : null;
                                $cart->group_addons = (isset($value->group_addons) && !empty($value->group_addons)) ? implode('|',$value->group_addons) : null;
                                $cart->totalAddonPrice =$value->totalAddonPrice;

                                $cart->save();
                            }
                            Session::forget('guest_cart');
                            $count=Cart::where('user_id',$user->id)->count();

                            Session::put('cart', $count);
                
                        }

                        if ($request['referral_code'] != "") {
                            $getdata=User::select('referral_amount')->where('type','1')->get()->first();

                            $wallet = $checkreferral->wallet + $getdata->referral_amount;

                            if ($wallet) {
                                $UpdateWalletDetails = User::where('id', $checkreferral->id)
                                ->update(['wallet' => $wallet]);

                                $from_Wallet = new Transaction;
                                $from_Wallet->user_id = $checkreferral->id;
                                $from_Wallet->order_id = null;
                                $from_Wallet->order_number = null;
                                $from_Wallet->wallet = $getdata->referral_amount;
                                $from_Wallet->payment_id = null;
                                $from_Wallet->order_type = '0';
                                $from_Wallet->transaction_type = '3';
                                $from_Wallet->username = $user->name;
                                $from_Wallet->save();
                            }

                            if ($getdata->referral_amount) {
                                $UpdateWallet = User::where('id', $user->id)
                                ->update(['wallet' => $getdata->referral_amount]);

                                $to_Wallet = new Transaction;
                                $to_Wallet->user_id = $user->id;
                                $to_Wallet->order_id = null;
                                $to_Wallet->order_number = null;
                                $to_Wallet->wallet = $getdata->referral_amount;
                                $to_Wallet->payment_id = null;
                                $to_Wallet->order_type = '0';
                                $to_Wallet->transaction_type = '3';
                                $to_Wallet->username = $checkreferral->name;
                                $to_Wallet->save();
                            }
                        }

                        if (env('Environment') == 'sendbox') {
                            session ( [
                                'mobile' => '+'.$request->country.''.$request->mobile,
                                'otp' => $otp,
                            ] );
                        } else {
                            session ( [
                                'mobile' => '+'.$request->country.''.$request->mobile,
                            ] );
                        }
                        
                        return Redirect::to('/otp-verify')->with('success', trans('messages.email_sent'));  
                    }catch(\Swift_TransportException $e){
                        $response = $e->getMessage() ;
                        return Redirect::back()->with('danger', trans('messages.email_error'));
                    }
                } else {
                    return redirect()->back()->with('danger', trans('messages.invalid_referral_code'));
                }
            }
            return redirect()->back()->with('danger', trans('messages.wrong'));
        }
    }

    public function otp_verify() {
        $getabout = About::where('id','=','1')->first();
        return view('front.otp-verify', compact('getabout'));
    }

    public function otp_verification(Request $request)
    {
        $validation = Validator::make($request->all(),[
            'mobile' => 'required',
            'otp' => 'required',
        ]);
        if ($validation->fails())
        {
            return Redirect::back()->withErrors($validation, 'otp-verify')->withInput();
        }
        else
        {
            $checkuser=User::where('mobile',$request->mobile)->first();
            $getdata=User::select('referral_amount')->where('type','1')->first();

            if (!empty($checkuser)) {
                $getconfiguration = OTPConfiguration::where('status','1')->first();

                if ($getconfiguration->name == "msg91") {
                    $curl = curl_init();

                    curl_setopt_array($curl, array(
                        CURLOPT_URL => "https://api.msg91.com/api/v5/otp/verify?authkey=".$getconfiguration->msg_authkey."&mobile=".$request->mobile."&otp=".$request->otp."",
                        CURLOPT_RETURNTRANSFER => true,
                        CURLOPT_ENCODING => "",
                        CURLOPT_MAXREDIRS => 10,
                        CURLOPT_TIMEOUT => 30,
                        CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
                        CURLOPT_CUSTOMREQUEST => "GET",
                    ));

                    $response = curl_exec($curl);
                    $err = curl_error($curl);

                    curl_close($curl);

                    $res = json_decode($response);

                    if ($res->type == "error") {
                        return Redirect::back()->with('danger', $res->message);
                    } else {
                        return Redirect::to('/');
                    }
                } else {
                    if ($checkuser->otp == $request->otp) {
                        $update=User::where('mobile',$request->mobile)->update(['otp'=>NULL,'is_verified'=>'1']);

                        $cart=Cart::where('user_id',$checkuser->id)->count();
                        session ( [ 
                            'id' => $checkuser->id, 
                            'name' => $checkuser->name,
                            'email' => $checkuser->email,
                            'mobile' => $checkuser->mobile,
                            'referral_code' => $checkuser->referral_code,
                            'referral_amount' => $getdata->referral_amount,
                            'profile_image' => 'unknown.png',
                            'cart' => $cart,
                        ] );

                        return Redirect::to('/');

                    } else {
                        return Redirect::back()->with('danger', trans("messages.invalid_otp"));
                    } 
                } 
            } else {
                return Redirect::back()->with('danger', trans("messages.invalid_email"));
            }            
        }
    }

    public function resend_otp()
    {
        $checkuser=User::where('mobile',Session::get('mobile'))->first();

        if (!empty($checkuser)) {
            try{
                $otp = rand ( 100000 , 999999 );

                $update=User::where('mobile',Session::get('mobile'))->update(['otp'=>$otp,'is_verified'=>'2']);

                $getconfiguration = OTPConfiguration::where('status','1')->first();
                if ($getconfiguration->name == "twilio") {
                    $sid    = $getconfiguration->twilio_sid; 
                    $token  = $getconfiguration->twilio_auth_token; 
                    $twilio = new Client($sid, $token); 
                    
                    $message = $twilio->messages 
                                ->create(Session::get('mobile'), // to 
                        array( 
                            "from" => $getconfiguration->twilio_mobile_number,
                            "body" => "Your Login Code ".$otp 
                       ) 
                    );
                }

                if ($getconfiguration->name == "msg91") {
                    $curl = curl_init();

                    curl_setopt_array($curl, array(
                        CURLOPT_URL => "https://api.msg91.com/api/v5/otp?template_id=".$getconfiguration->msg_template_id."&mobile=".Session::get('mobile')."&authkey=".$getconfiguration->msg_authkey."",
                        CURLOPT_RETURNTRANSFER => true,
                        CURLOPT_ENCODING => "",
                        CURLOPT_MAXREDIRS => 10,
                        CURLOPT_TIMEOUT => 30,
                        CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
                        CURLOPT_CUSTOMREQUEST => "GET",
                        CURLOPT_HTTPHEADER => array(
                            "content-type: application/json"
                        ),
                    ));

                    $response = curl_exec($curl);
                    $err = curl_error($curl);

                    curl_close($curl);

                    $res = json_decode($response);

                    if ($res->type == "error") {
                        return Redirect::back()->with('danger', $res->message);
                    }
                }

                if (env('Environment') == 'sendbox') {
                    session ( [
                        'otp' => $otp,
                    ] );
                }

                return Redirect::to('/otp-verify')->with('success', trans("messages.email_sent"));
            }catch(\Swift_TransportException $e){
                $response = $e->getMessage() ;
                // return Redirect::back()->with('danger', $response);
                return Redirect::back()->with('danger', trans("messages.email_error"));
            }

        } else {
            return Redirect::back()->with('danger', trans("messages.invalid_email"));
        }
    }

    public function editProfile(request $request)
    {
        $validation = Validator::make($request->all(),[
          'name' => 'required',
          'profile_image' => 'image|mimes:jpeg,png,jpg',
        ]);

        $error_array = array();
        $success_output = '';
        if ($validation->fails())
        {
            foreach($validation->messages()->getMessages() as $field_name => $messages)
            {
                $error_array[] = $messages;
            }
        }
        else
        {
            $profile = new User;
            $profile->exists = true;
            $profile->id = Session::get('id');

            if(isset($request->profile_image)){
                if($request->hasFile('profile_image')){
                    $profile_image = $request->file('profile_image');
                    $profile_image = 'profile-' . uniqid() . '.' . $request->profile_image->getClientOriginalExtension();
                    $request->profile_image->move('storage/app/public/images/profile/', $profile_image);
                    $profile->profile_image=$profile_image;
                }  
                session ( [ 
                    'profile_image' => $profile_image,
                ] );
            }
            $profile->name =$request->name;
            $profile->save();           

            session ( [ 
                'name' => $request->name,
            ] );
            
            $success_output = 'profile updated Successfully!';
        }
        $output = array(
            'error'     =>  $error_array,
            'success'   =>  $success_output
        );
        echo json_encode($output);

    }

    public function addreview(request $request)
    {
        $validation = \Validator::make($request->all(), [
            'user_id' => 'required|unique:ratting',
            'ratting'=>'required',
            'comment'=>'required',
        ],[
            'user_id.unique'=>trans("messages.review_done"),
            'ratting.required'=>trans("messages.ratting_required"),
            'comment.required'=>trans("messages.comment_required")
        ]);
         
        $error_array = array();
        $success_output = '';
        if ($validation->fails())
        {
            foreach($validation->messages()->getMessages() as $field_name => $messages)
            {
                $error_array[] = $messages;
            }
        }
        else
        {
            $user = new Ratting;
            $user->user_id =$request->user_id;
            $user->ratting =$request->ratting;
            $user->comment =$request->comment;
            $user->save();
            Session::flash('message', '<div class="alert alert-success">{{trans("messages.review_added")}} </div>');
        }
        $output = array(
            'error'     =>  $error_array,
            'success'   =>  $success_output
        );
        
        return json_encode($output);  

    }
}
