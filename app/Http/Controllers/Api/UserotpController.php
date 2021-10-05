<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Mail;
use App\User;
use App\Transaction;
use App\About;
use App\OTPConfiguration;
use App\Contact;
use Validator;
use Twilio\Rest\Client;
use Session;
use App\Item;
use App\ItemImages;
use App\Cart; 

class UserotpController extends Controller
{
    public function register(Request $request )
    {
        $checkemail=User::where('email',$request->email)->first();
        $checkmobile=User::where('mobile',$request->mobile)->first();

        $str_result = 'ABCDEFGHIJKLMNOPQRSTUVWXYZ1234567890abcdefghijklmnopqrstuvwxyz'; 
        $referral_code = substr(str_shuffle($str_result), 0, 10); 
        $otp = rand ( 100000 , 999999 );

        if ($request->register_type == "email") {
            if($request->email == ""){
                return response()->json(["status"=>0,"message"=>trans('messages.email_required')],400);
            }
            if($request->name == ""){
                return response()->json(["status"=>0,"message"=>trans('messages.full_name_required')],400);
            }
            if($request->mobile == ""){
                return response()->json(["status"=>0,"message"=>trans('messages.mobile_required')],400);
            }
            if($request->token == ""){
                return response()->json(["status"=>0,"message"=>trans('messages.token_required')],400);
            }

            if(!empty($checkemail))
            {
                return response()->json(['status'=>0,'message'=>trans('messages.email_exist')],400);
            }

            if(!empty($checkmobile))
            {
                return response()->json(['status'=>0,'message'=>trans('messages.mobile_exist')],400);
            }

            if ($request->login_type == "google" OR $request->login_type == "facebook") {
                $password = "";
            } else {
                $password = Hash::make($request->get('password'));
            }

            $getdata=User::select('referral_amount','firebase','currency')->where('type','1')->get()->first();

            $checkreferral=User::select('id','name','referral_code','wallet','email','token')->where('referral_code',$request['referral_code'])->first();

            if (@$checkreferral->referral_code == $request['referral_code']) {
                          
                $data['name']=$request->get('name');
                $data['mobile']=$request->get('mobile');
                $data['email']=$request->get('email');
                $data['profile_image']='unknown.png';
                $data['password']=$password;
                $data['token'] = $request->get('token');
                $data['login_type']=$request->get('login_type');
                $data['google_id']=$request->get('google_id');
                $data['facebook_id']=$request->get('facebook_id');
                $data['referral_code']=$referral_code;
                $data['otp']=$otp;
                $data['type']='2';

                $user=User::create($data);

                $otp = rand ( 100000 , 999999 );

                $update=User::where('email',$request['email'])->update(['otp'=>$otp,'is_verified'=>'2','token'=>$request->get('token')]);

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
                    $err = curl_error($curl);

                    curl_close($curl);
                }

                $wallet = $checkreferral->wallet + $getdata->referral_amount;
 
                if ($request['referral_code'] != "") {
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

                       //Notification
                       try{

                            $title = "Referral Earning";

                           $body = 'Your friend "'.$name.'" has used your referral code to register with Restaurant User. You have earned "'.$getdata->currency.''.number_format($getdata->referral_amount,2).'" referral amount in your wallet.';
                           $google_api_key = $getdata->firebase; 
                           
                           $registrationIds = $checkreferral->token;
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
                

                if($user)
                {
                    $arrayName = array(
                        'id' => $user->id,
                        'name' => $user->name,
                        'mobile' => $user->mobile,
                        'email' => $user->email,
                        'login_type' => $user->login_type,
                        'referral_code' => $user->referral_code,
                        'profile_image' => url('/storage/app/public/images/profile/'.$user->profile_image),
                    );


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

                    return response()->json(['status'=>1,'message'=>'Registration Successful','data'=>$arrayName],200);
                }
                else
                {
                    return response()->json(['status'=>0,'message'=>trans('messages.wrong')],400);
                }

            } else {
                return response()->json(['status'=>0,'message'=>trans('messages.invalid_referral_code')],200);
            }
            
        }
        if ($request->login_type == "google") {
            if($request->email == ""){
                return response()->json(["status"=>0,"message"=>trans('messages.email_required')],400);
            }
            if($request->name == ""){
                return response()->json(["status"=>0,"message"=>trans('messages.full_name_required')],400);
            }
            if($request->token == ""){
                return response()->json(["status"=>0,"message"=>trans('messages.token_required')],400);
            }
            if($request->google_id == ""){
                return response()->json(["status"=>0,"message"=>trans('messages.google_id_required')],400);
            }

            $usergoogle=User::where('google_id',$request->google_id)->first();
            if ($usergoogle != "" OR @$usergoogle->email == $request->email AND $request->email != "") {
                if ($usergoogle->mobile == "") {
                    $arrayName = array(
                        'id' => $usergoogle->id
                    );
                    return response()->json(['status'=>2,'message'=>trans('messages.mobile_required'),'data'=>$arrayName],200);
                } else {
                    if($usergoogle->is_verified == '1') 
                    {
                        if($usergoogle->is_available == '1') 
                        {
                            $arrayName = array(
                                'id' => $usergoogle->id,
                                'name' => $usergoogle->name,
                                'mobile' => $usergoogle->mobile,
                                'email' => $usergoogle->email,
                                'login_type' => $usergoogle->login_type,
                                'referral_code' => $usergoogle->referral_code,
                                'profile_image' => url('/storage/app/public/images/profile/'.$usergoogle->profile_image),
                            );

                            $update=User::where('email',$usergoogle->mobile)->update(['token'=>$request->token]);
                            return response()->json(['status'=>1,'message'=>'Login Successful','data'=>$arrayName],200);
                        } else {
                            return response()->json(['status'=>0,'message'=>trans('messages.blocked')],200);
                        }
                    } else {
                        $getconfiguration = OTPConfiguration::where('status','1')->first();
                        if ($getconfiguration->name == "twilio") {
                            $sid    = $getconfiguration->twilio_sid; 
                            $token  = $getconfiguration->twilio_auth_token; 
                            $twilio = new Client($sid, $token); 
                            
                            $message = $twilio->messages 
                                        ->create($usergoogle->mobile, // to 
                                array( 
                                    "from" => $getconfiguration->twilio_mobile_number,
                                    "body" => "Your Login Code ".$otp 
                               ) 
                            );
                        }

                        if ($getconfiguration->name == "msg91") {
                            $curl = curl_init();

                            curl_setopt_array($curl, array(
                                CURLOPT_URL => "https://api.msg91.com/api/v5/otp?template_id=".$getconfiguration->msg_template_id."&mobile=".$usergoogle->mobile."&authkey=".$getconfiguration->msg_authkey."",
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

                        $update=User::where('email',$usergoogle->mobile)->update(['otp'=>$otp]);

                        $status=3;
                        $message=trans('messages.unverified');
                        return response()->json(['status'=>$status,'message'=>$message,'email'=>$usergoogle->mobile],200);
                    }
                }
            } else {

                return response()->json(['status'=>2,'message'=>'Successful'],200);

            }
        } elseif ($request->login_type == "facebook") {
            if($request->email == ""){
                return response()->json(["status"=>0,"message"=>trans('messages.email_required')],400);
            }
            if($request->name == ""){
                return response()->json(["status"=>0,"message"=>trans('messages.full_name_required')],400);
            }
            if($request->token == ""){
                return response()->json(["status"=>0,"message"=>trans('messages.token_required')],400);
            }
            if($request->facebook_id == ""){
                return response()->json(["status"=>0,"message"=>trans('messages.facebook_id_required')],400);
            }

            $userfacebook=User::where('users.facebook_id',$request->facebook_id)->first();

            if ($userfacebook != "" OR @$userfacebook->email == $request->email AND $request->email != "") {
                if ($userfacebook->mobile == "") {
                    $arrayName = array(
                        'id' => $userfacebook->id
                    );
                    return response()->json(['status'=>2,'message'=>trans('messages.mobile_required'),'data'=>$arrayName],200);
                } else {
                    if($userfacebook->is_verified == '1') 
                    {
                        if($userfacebook->is_available == '1') 
                        {
                            $arrayName = array(
                                'id' => $userfacebook->id,
                                'name' => $userfacebook->name,
                                'mobile' => $userfacebook->mobile,
                                'email' => $userfacebook->email,
                                'login_type' => $userfacebook->login_type,
                                'referral_code' => $userfacebook->referral_code,
                                'profile_image' => url('/storage/app/public/images/profile/'.$userfacebook->profile_image),
                            );
                            $update=User::where('email',$userfacebook['email'])->update(['token'=>$request->token]);
                            return response()->json(['status'=>1,'message'=>'Login Successful','data'=>$arrayName],200);
                        } else {
                            return response()->json(['status'=>0,'message'=>trans('messages.blocked')],200);
                        }
                        
                    } else {
                        $getconfiguration = OTPConfiguration::where('status','1')->first();
                        if ($getconfiguration->name == "twilio") {
                            $sid    = $getconfiguration->twilio_sid; 
                            $token  = $getconfiguration->twilio_auth_token; 
                            $twilio = new Client($sid, $token); 
                            
                            $message = $twilio->messages 
                                        ->create($userfacebook->mobile, // to 
                                array( 
                                    "from" => $getconfiguration->twilio_mobile_number,
                                    "body" => "Your Login Code ".$otp 
                               ) 
                            );
                        }

                        if ($getconfiguration->name == "msg91") {
                            $curl = curl_init();

                            curl_setopt_array($curl, array(
                                CURLOPT_URL => "https://api.msg91.com/api/v5/otp?template_id=".$getconfiguration->msg_template_id."&mobile=".$userfacebook->mobile."&authkey=".$getconfiguration->msg_authkey."",
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

                        $update=User::where('email',$userfacebook->email)->update(['otp'=>$otp]);

                        $status=3;
                        $message=trans('messages.unverified');
                        return response()->json(['status'=>$status,'message'=>$message,'email'=>$userfacebook->email],422);
                    }
                }
            } else {
                
                if(!empty($checkemail))
                {
                    return response()->json(['status'=>0,'message'=>trans('messages.email_exist')],400);
                }

                return response()->json(['status'=>2,'message'=>'Successful'],200);

            }
        }
    }

    public function otpverify(Request $request )
    {
        if($request->mobile == ""){
            return response()->json(["status"=>0,"message"=>trans('messages.mobile_required')],400);
        }
        if($request->otp == ""){
            return response()->json(["status"=>0,"message"=>trans('messages.otp_required')],400);
        }
        if($request->token == ""){
            return response()->json(["status"=>0,"message"=>trans('messages.token_required')],400);
        }

        $checkuser=User::where('mobile',$request->mobile)->first();

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
                    return response()->json(["status"=>0,"message"=>trans('messages.invalid_otp')],400);
                } else {
                    $arrayName = array(
                        'id' => $checkuser->id,
                        'name' => $checkuser->name,
                        'mobile' => $checkuser->mobile,
                        'email' => $checkuser->email,
                        'login_type' => $checkuser->login_type,
                        'referral_code' => $checkuser->referral_code,
                        'profile_image' => url('/public/images/profile/'.$checkuser->profile_image),
                    );

                    return response()->json(['status'=>1,'message'=>"Email is verified",'data'=>$arrayName],200);
                }
            } else {
                if ($checkuser->otp == $request->otp) {
                    $update=User::where('mobile',$request->mobile)->update(['otp'=>NULL,'is_verified'=>'1','token'=>$request->token]);

                    $arrayName = array(
                        'id' => $checkuser->id,
                        'name' => $checkuser->name,
                        'mobile' => $checkuser->mobile,
                        'email' => $checkuser->email,
                        'login_type' => $checkuser->login_type,
                        'referral_code' => $checkuser->referral_code,
                        'profile_image' => url('/public/images/profile/'.$checkuser->profile_image),
                    );

                    return response()->json(['status'=>1,'message'=>"Email is verified",'data'=>$arrayName],200);

                } else {
                    return response()->json(["status"=>0,"message"=>trans('messages.invalid_otp')],400);
                } 
            } 
             
        } else {
            return response()->json(["status"=>0,"message"=>trans('messages.invalid_email')],400);
        }  
    }

    public function resendotp(Request $request )
    {
        if($request->mobile == ""){
            return response()->json(["status"=>0,"message"=>trans('messages.mobile_required')],400);
        }

        $checkuser=User::where('mobile',$request->mobile)->first();

        if (!empty($checkuser)) {           

            try{
                $otp = rand ( 100000 , 999999 );

                $update=User::where('mobile',$request->mobile)->update(['otp'=>$otp,'is_verified'=>'2']);

                $getconfiguration = OTPConfiguration::where('status','1')->first();
                if ($getconfiguration->name == "twilio") {
                    $sid    = $getconfiguration->twilio_sid; 
                    $token  = $getconfiguration->twilio_auth_token; 
                    $twilio = new Client($sid, $token); 
                    
                    $message = $twilio->messages 
                                ->create($request->mobile, // to 
                        array( 
                            "from" => $getconfiguration->twilio_mobile_number,
                            "body" => "Your Login Code ".$otp 
                       ) 
                    );
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
                    $err = curl_error($curl);

                    curl_close($curl);
                }

                return response()->json(["status"=>1,"message"=>trans('messages.email_sent'),'otp'=>$otp],200);
            }catch(\Swift_TransportException $e){
                $response = $e->getMessage() ;
                return response()->json(['status'=>0,'message'=>trans('messages.email_error')],200);
            }

        } else {
            return response()->json(["status"=>0,"message"=>trans('messages.invalid_email')],400);
        }  
    }

    public function login(Request $request )
    {
        if($request->mobile == ""){
            return response()->json(["status"=>0,"message"=>trans('messages.mobile_required')],400);
        }
        
        $login=User::where('mobile',$request->mobile)->where('type','=','2')->first();

        if(!empty($login))
        {
            if($login->is_available == '1') 
            {
                $otp = rand ( 100000 , 999999 );
                
                $getconfiguration = OTPConfiguration::where('status','1')->first();
                if ($getconfiguration->name == "twilio") {
                    $sid    = $getconfiguration->twilio_sid; 
                    $token  = $getconfiguration->twilio_auth_token; 
                    $twilio = new Client($sid, $token); 
                    
                    $message = $twilio->messages 
                                ->create($request->mobile, // to 
                        array( 
                            "from" => $getconfiguration->twilio_mobile_number,
                            "body" => "Your Login Code ".$otp 
                       ) 
                    );
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
                    $err = curl_error($curl);

                    curl_close($curl);
                }

                $data_token['otp'] = $otp;
                $update=User::where('mobile',$request->mobile)->update($data_token);
                $status=2;
                $message=trans('messages.unverified');

                return response()->json(['status'=>$status,'message'=>$message,'mobile'=>$login->mobile],200);
            }
            else
            {
                $status=0;
                $message=trans('messages.blocked');
                return response()->json(['status'=>$status,'message'=>$message],422);
            }
        }
        else
        {
            $status=0;
            $message=trans('messages.invalid_mobile');
            $data="";
            return response()->json(['status'=>$status,'message'=>$message],422);
        }
        
       
        return response()->json(['status'=>$status,'message'=>$message,'data'=>$data],200);
    }

    public function AddMobile(Request $request)
    {
        if($request->mobile == ""){
            return response()->json(["status"=>0,"message"=>trans('messages.mobile_required')],400);
        }
        if($request->user_id == ""){
            return response()->json(["status"=>0,"message"=>trans('messages.user_id_required')],400);
        }

        $checkmobile=User::where('mobile',$request['mobile'])->first();
        
        if(!empty($checkmobile))
        {
            return response()->json(['status'=>0,'message'=>trans('messages.mobile_exist')],400);
        }

        try {
            $update=User::where('id',$request['user_id'])->update($data);
            return response()->json(["status"=>1,"message"=>trans('messages.update')],200);

        } catch (\Exception $e){
            return response()->json(['status'=>0,'message'=>trans('messages.wrong')],400);
        }
    }

    public function getprofile(Request $request )
    {
        if($request->user_id == ""){
            return response()->json(["status"=>0,"message"=>trans('messages.user_id_required')],400);
        }

        $users = User::where('id',$request['user_id'])->first();
        $admin = About::select('email','mobile','address','fb','twitter','insta')->where('id','1')->first();

        if ($users->mobile == "") {
            $mobile = "";
        } else {
            $mobile = $users->mobile;
        }

        $arrayName = array(
            'id' => $users->id,
            'name' => $users->name,
            'mobile' => $mobile,
            'email' => $users->email,
            'login_type' => $users->login_type,
            'profile_image' => url('/storage/app/public/images/profile/'.$users->profile_image)
        );


        if(!empty($arrayName))
        {
            return response()->json(['status'=>1,'message'=>'Profile data','data'=>$arrayName,'admin'=>$admin],200);
        } else {
            return response()->json(['status'=>0,'message'=>trans('messages.no_data')],422);
        }

        return response()->json(['status'=>0,'message'=>trans('messages.wrong')],400);
    }

    public function editprofile(Request $request )
    {
        if($request->user_id == ""){
            return response()->json(["status"=>0,"message"=>trans('messages.user_id_required')],400);
        }
        if($request->name == ""){
            return response()->json(["status"=>0,"message"=>trans('messages.full_name_required')],400);
        }

        $user = new User;
        $user->exists = true;
        $user->id = $request->user_id;

        if(isset($request->image)){
            if($request->hasFile('image')){
                $image = $request->file('image');
                $image = 'profile-' . uniqid() . '.' . $request->image->getClientOriginalExtension();
                $request->image->move('storage/app/public/images/profile', $image);
                $user->profile_image=$image;
            }            
        }
        $user->name =$request->name;
        $user->save();

        $user_details=User::where('id',$request->user_id)->where('type','=','2')->first();
        $arrayName = array(
            'id' => $user_details->id,
            'name' => $user_details->name,
            'mobile' => $user_details->mobile,
            'email' => $user_details->email,
            'login_type' => $user_details->login_type,
            'profile_image' => url('/storage/app/public/images/profile/'.$user_details->profile_image),
        );

        if($user)
        {
            return response()->json(['status'=>1,'message'=>'Profile has been updated','data'=>$arrayName],200);
        }
        else
        {
            return response()->json(['status'=>0,'message'=>trans('messages.wrong')],400);
        }
    }

    public function changepassword(Request $request)
    {
        if($request->user_id == ""){
            return response()->json(["status"=>0,"message"=>trans('messages.user_id_required')],400);
        }
        if($request->old_password == ""){
            return response()->json(["status"=>0,"message"=>trans('messages.old_password_required')],400);
        }
        if($request->new_password == ""){
            return response()->json(["status"=>0,"message"=>trans('messages.new_password_required')],400);
        }
        if($request['old_password']==$request['new_password'])
        {
            return response()->json(['status'=>0,'message'=>trans('messages.old_password_diffrent')],400);
        }
        $check_user=User::where('id',$request['user_id'])->get()->first();
        if(Hash::check($request['old_password'],$check_user->password))
        {
            $data['password']=Hash::make($request['new_password']);
            $update=User::where('id',$request['user_id'])->update($data);
            return response()->json(['status'=>1,'message'=>trans('messages.update')],200);
        }
        else{
            return response()->json(['status'=>0,'message'=>trans('messages.invalid_password')],400);
        }
    }

    public function restaurantslocation(Request $request)
    {
        $trucklocation=User::select('lat','lang')->where('type','1')->first();
        if(!empty($trucklocation))
        {
            return response()->json(['status'=>1,'message'=>'Location','data'=>$trucklocation],200);
        }
        else
        {
            return response()->json(['status'=>0,'message'=>trans('messages.no_data')],200);
        }
    }

    public function isopen(Request $request)
    {
        $isopen=User::select('is_open')->where('type','1')->first();

        if(!empty($isopen))
        {
            if ($isopen->is_open == "1") {
                return response()->json(['status'=>1,'message'=>trans('messages.restaurant_open')],200);
            } else {
                return response()->json(['status'=>0,'message'=>trans('messages.restaurant_closed')],200);
            }
        }
        else
        {
            return response()->json(['status'=>0,'message'=>trans('messages.wrong')],200);
        }
    }

    public function forgotPassword(Request $request)
    {
        if($request->email == ""){
            return response()->json(["status"=>0,"message"=>trans('messages.email_required')],400);
        }

        $checklogin=User::where('email',$request['email'])->first();
        
        if(empty($checklogin))
        {
            return response()->json(['status'=>0,'message'=>trans('messages.invalid_email')],400);
        } elseif ($checklogin->google_id != "" OR $checklogin->facebook_id != "") {
            return response()->json(['status'=>0,'message'=>trans('messages.social_login')],200);
        } else {
            try{
                $password = substr(str_shuffle('abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ0123456789') , 0 , 8 );
                $newpassword['password'] = Hash::make($password);
                $update = User::where('email', $request['email'])->update($newpassword);

                $getlogo = About::select('logo')->where('id','=','1')->first();

                $title='Password Reset';
                $email=$checklogin->email;
                $name=$checklogin->name;
                $logo=$getlogo->logo;
                $data=['title'=>$title,'email'=>$email,'name'=>$name,'password'=>$password,'logo'=>$logo];

                Mail::send('Email.email',$data,function($message)use($data){
                    $message->from(env('MAIL_USERNAME'))->subject($data['title']);
                    $message->to($data['email']);
                } );
                return response()->json(['status'=>1,'message'=>trans('messages.password_sent')],200);
            }catch(\Swift_TransportException $e){
                $response = $e->getMessage() ;
                return response()->json(['status'=>0,'message'=>trans('messages.email_error')],200);
            }
        }

    }

    public function contact(Request $request)
    {
        if($request->firstname == ""){
            return response()->json(["status"=>0,"message"=>trans('messages.first_name_required')],400);
        }
        if($request->lastname == ""){
            return response()->json(["status"=>0,"message"=>trans('messages.last_name_required')],400);
        }
        if($request->email == ""){
            return response()->json(["status"=>0,"message"=>trans('messages.email_required')],400);
        }
        if($request->message == ""){
            return response()->json(["status"=>0,"message"=>trans('messages.message_required')],400);
        }

        $contact = new Contact;
        $contact->firstname = $request->firstname;
        $contact->lastname = $request->lastname;
        $contact->email = $request->email;
        $contact->message = $request->message;
        $contact->save();

        if ($contact) {
          return response()->json(['status'=>1,'message'=>'Success'],200);
        } else {
          return response()->json(['status'=>0,'message'=>trans('messages.wrong')],200);
        }
    }

    public function checkaddons()
    {
        if (\App\SystemAddons::where('unique_identifier', 'otp')->first() != null && \App\SystemAddons::where('unique_identifier', 'otp')->first()->activated) {
            return response()->json(['status'=>1,'data'=>'mobile'],200);
        } else {
            return response()->json(['status'=>1,'data'=>'email'],200);
        }
    }
}