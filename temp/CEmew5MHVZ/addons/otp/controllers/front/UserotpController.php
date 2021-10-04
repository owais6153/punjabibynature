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

class UserotpController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
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
