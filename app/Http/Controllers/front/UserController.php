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
use App\About;
use App\Transaction;
use App\Cart;
use App\Address;
use App\Pincode;
use App\Payment;
use App\Category;
use Validator;
use App\Item;
use App\ItemImages;

class UserController extends Controller
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
        $login=User::where('email',$request['email'])->where('type','=','2')->first();

        $getdata=User::select('referral_amount')->where('type','1')->first();
        
        if(!empty($login)) {
        
            if(Hash::check($request->get('password'),$login->password)) {   
                if($login->is_verified == '1') {
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
                            'login_type' => $login->login_type,
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
                                $cart->user_id =$login->id;
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
                            $count=Cart::where('user_id',$login->id)->count();

                            Session::put('cart', $count);
                
                        }


                        return Redirect::to('/');
                    } else {
                        return Redirect::back()->with('danger', trans('messages.blocked'));
                    }
                } else {

                    $otp = rand ( 100000 , 999999 );
                    try{
                        $getlogo = About::select('logo')->where('id','=','1')->first();

                        $title=trans('messages.email_code');
                        $email=$request->email;
                        $logo=$getlogo->logo;
                        $data=['title'=>$title,'email'=>$email,'otp'=>$otp,'logo'=>$logo];

                        Mail::send('Email.emailverification',$data,function($message)use($data){
                            $message->from(env('MAIL_USERNAME'))->subject($data['title']);
                            $message->to($data['email']);
                        } );

                        $update=User::where('email',$request->email)->update(['otp'=>$otp,'is_verified'=>'2']);

                        if (env('Environment') == 'sendbox') {
                            session ( [
                                'email' => $login->email,
                                'password' => $login->password,
                                'otp' => $otp,
                            ] );
                        } else {
                            session ( [
                                'email' => $login->email,
                            ] );
                        }

                    }catch(\Swift_TransportException $e){
                        $response = $e->getMessage() ;
                        return Redirect::back()->with('danger', trans('messages.email_error'));
                    }
                    return Redirect::to('/otp-verify')->with('success', trans('messages.email_sent'));
                }
            } else {
                return Redirect::back()->with('danger', trans('messages.password_required'));
            }
        } else {
            return Redirect::back()->with('danger', trans('messages.invalid_email'));
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

                    $users=User::where('email',$request->email)->get()->first();
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

                        $getlogo = About::select('logo')->where('id','=','1')->first();

                        $title=trans('messages.email_code');
                        $email=$request->email;
                        $logo=$getlogo->logo;
                        $data=['title'=>$title,'email'=>$email,'otp'=>$otp,'logo'=>$logo];

                        Mail::send('Email.emailverification',$data,function($message)use($data){
                            $message->from(env('MAIL_USERNAME'))->subject($data['title']);
                            $message->to($data['email']);
                        } );

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
                                'email' => $request->email,
                                'otp' => $otp,
                            ] );
                        } else {
                            session ( [
                                'email' => $request->email,
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
        } else {
            $validation = Validator::make($request->all(),[
                'name' => 'required',
                'email' => 'required|unique:users',
                'mobile' => 'required|unique:users',
                'password' => 'required|confirmed',
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
                        
                       $getlogo = About::select('logo')->where('id','=','1')->first();

                       $title=trans('messages.email_code');
                       $email=$request->email;
                       $logo=$getlogo->logo;
                       $data=['title'=>$title,'email'=>$email,'otp'=>$otp,'logo'=>$logo];

                        // Mail::send('Email.emailverification',$data,function($message)use($data){
                        //     $message->from(env('MAIL_USERNAME'))->subject($data['title']);
                        //     $message->to($data['email']);
                        // } );

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
                                'email' => $request->email,
                                'otp' => $otp,
                            ] );
                        } else {
                            session ( [
                                'email' => $request->email,
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

    public function changePassword(request $request)
    {
        $validation = \Validator::make($request->all(), [
            'oldpassword'=>'required|min:6',
            'newpassword'=>'required|min:6',
            'confirmpassword'=>'required_with:newpassword|same:newpassword|min:6',
        ],[
            'oldpassword.required'=>'Old Password is required',
            'newpassword.required'=>'New Password is required',
            'confirmpassword.required'=>'Confirm Password is required'
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
        else if($request->oldpassword==$request->newpassword)
        {
            $error_array[]='Old and new password must be different';
        }
        else
        {
            $login=User::where('id','=',Session::get('id'))->first();

            if(\Hash::check($request->oldpassword,$login->password)){
                $data['password'] = Hash::make($request->newpassword);
                User::where('id', Session::get('id'))->update($data);
                Session::flash('message', '<div class="alert alert-success"><strong>Success!</strong> {{trans("messages.password_changed")}} </div>');
            }else{
                $error_array[]=trans("messages.old_password_invalid");
            }
        }
        $output = array(
            'error'     =>  $error_array,
            'success'   =>  $success_output
        );
        return json_encode($output);  

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

    public function forgot_password() {
        $getabout = About::where('id','=','1')->first();
        return view('front.forgot-password', compact('getabout'));
    }

    public function forgotpassword(Request $request)
    {
        $validation = Validator::make($request->all(),[
            'email' => 'required'
        ]);
        if ($validation->fails())
        {
            return Redirect::back()->withErrors($validation, 'login')->withInput();
        }
        else
        {
            $checklogin=User::where('email',$request->email)->first();
            
            if(empty($checklogin))
            {
                return Redirect::back()->with('danger', trans("messages.invalid_email"));
            } else {
                if ($checklogin->google_id != "" OR $checklogin->facebook_id != "") {
                    return Redirect::back()->with('danger', trans("messages.social_account"));
                } else {
                    try{
                        $password = substr(str_shuffle('abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ0123456789') , 0 , 8 );

                        $newpassword['password'] = Hash::make($password);
                        $update = User::where('email', $request['email'])->update($newpassword);

                        $getlogo = About::select('logo')->where('id','=','1')->first();

                        $title=trans('messages.email_code');
                        $email=$request->email;
                        $name=$checklogin->name;
                        $logo=$getlogo->logo;
                        
                        $data=['title'=>$title,'email'=>$email,'name'=>$name,'password'=>$password,'logo'=>$logo];

                        Mail::send('Email.email',$data,function($message)use($data){
                            $message->from(env('MAIL_USERNAME'))->subject($data['title']);
                            $message->to($data['email']);
                        } );
                        return Redirect::back()->with('success', trans("messages.password_sent"));
                    }catch(\Swift_TransportException $e){
                        $response = $e->getMessage() ;
                        return Redirect::back()->with('danger', trans("messages.email_error"));
                    }
                }
            }
        }
        return Redirect::back()->with('danger', trans('messages.wrong')); 
    }

    public function otp_verify() {
        $getabout = About::where('id','=','1')->first();
        return view('front.otp-verify', compact('getabout'));
    }

    public function otp_verification(Request $request)
    {
        $validation = Validator::make($request->all(),[
            'email' => 'required',
            'otp' => 'required',
        ]);
        if ($validation->fails())
        {
            return Redirect::back()->withErrors($validation, 'otp-verify')->withInput();
        }
        else
        {
            $checkuser=User::where('email',$request->email)->first();
            $getdata=User::select('referral_amount')->where('type','1')->first();

            if (!empty($checkuser)) {
                if ($checkuser->otp == $request->otp) {
                    $update=User::where('email',$request['email'])->update(['otp'=>NULL,'is_verified'=>'1']);

                    $cart=Cart::where('user_id',$checkuser->id)->count();
                    session ( [ 
                        'id' => $checkuser->id, 
                        'name' => $checkuser->name,
                        'email' => $checkuser->email,
                        'mobile' => $checkuser->mobile,
                        'referral_code' => $checkuser->referral_code,
                        'referral_amount' => $getdata->referral_amount,
                        'login_type' => $checkuser->login_type,
                        'profile_image' => 'unknown.png',
                        'cart' => $cart,
                    ] );

                    return Redirect::to('/');

                } else {
                    return Redirect::back()->with('danger', trans("messages.invalid_otp"));
                }  
            } else {
                return Redirect::back()->with('danger', trans("messages.invalid_email"));
            }            
        }
        return Redirect::back()->with('danger', trans('messages.wrong')); 
    }

    public function resend_otp()
    {
        $checkuser=User::where('email',Session::get('email'))->first();

        if (!empty($checkuser)) {
            try{
                $otp = rand ( 100000 , 999999 );

                $update=User::where('email',Session::get('email'))->update(['otp'=>$otp,'is_verified'=>'2']);

                $getlogo = About::select('logo')->where('id','=','1')->first();

                $title=trans('messages.email_code');
                $email=Session::get('email');
                $logo=$getlogo->logo;
                $data=['title'=>$title,'email'=>$email,'otp'=>$otp,'logo'=>$logo];
                
                Mail::send('Email.emailverification',$data,function($message)use($data){
                    $message->from(env('MAIL_USERNAME'))->subject($data['title']);
                    $message->to($data['email']);
                } );
            }catch(\Swift_TransportException $e){
                $response = $e->getMessage() ;
                return Redirect::back()->with('danger', trans("messages.email_error"));
            }

            if (env('Environment') == 'sendbox') {
                session ( [
                    'otp' => $otp,
                ] );
            }

            return Redirect::to('/otp-verify')->with('success', trans("messages.email_sent"));

        } else {
            return Redirect::back()->with('danger', trans("messages.invalid_email"));
        }  
    }

    public function wallet(Request $request)
    {

        $walletamount=User::select('name','email','mobile','wallet')->where('id',Session::get('id'))->first();

        $transaction_data=Transaction::select('order_number','order_type','transaction_type','wallet',DB::raw('DATE_FORMAT(created_at, "%d %M %Y") as date'),'username','order_id')->where('user_id',Session::get('id'))->orderBy('id', 'DESC')->paginate(6);

        $getabout = About::where('id','=','1')->first();

        $getdata=User::select('currency')->where('type','1')->first();

        $getpaymentdata=Payment::select('payment_name','test_public_key','live_public_key','environment')->where('is_available','1')->orderBy('id', 'DESC')->get();


        $user_id = Session::get('id');  
        if (Session::get('id')) {
            $cartdata=Cart::with('itemimage')->select('id','qty','price','item_notes','cart.variation','item_name','tax',\DB::raw("CONCAT('".url('/storage/app/public/images/item/')."/', item_image) AS item_image"),'item_id','addons_id','addons_name','addons_price')
            ->where('user_id',$user_id)
            ->where('is_available','=','1')->get();
        }
        else{
            $cartdata_temp = Session::get('guest_cart');
            $cartdata = json_decode(json_encode($cartdata_temp));       
        }
        $getcategory = Category::where('is_available','=','1')->where('is_deleted','2')->get();

        return view('front.wallet', compact('getabout','transaction_data','walletamount','getdata','getpaymentdata', 'getcategory', 'cartdata'));

    }

    public function address(Request $request)
    {
        $addressdata=Address::where('user_id',Session::get('id'))->orderBy('id', 'DESC')->paginate(6);

        $getabout = About::where('id','=','1')->first();

        $getdata=User::select('currency','map')->where('type','1')->first();

        $getpincode = Pincode::get();
                     if (Session::get('id')) {
                $user_id = Session::get('id');

            $cartdata=Cart::with('itemimage')->select('id','qty','price','item_notes','cart.variation','item_name','tax',\DB::raw("CONCAT('".url('/storage/app/public/images/item/')."/', item_image) AS item_image"),'item_id','addons_id','addons_name','addons_price')
            ->where('user_id',$user_id)
            ->where('is_available','=','1')->get();
        }
        else{
            $cartdata_temp = Session::get('guest_cart');
            $cartdata = json_decode(json_encode($cartdata_temp));
                    }
                    $getcategory = Category::where('is_available','=','1')->where('is_deleted','2')->get();
        return view('front.address', compact('getabout','addressdata','getdata','getpincode', 'cartdata', 'getcategory'));
    }

    public function show(Request $request)
    {
        $getaddress = Address::where('id',$request->id)->first();
        return response()->json(['ResponseCode' => 1, 'ResponseText' => trans("messages.success"), 'ResponseData' => $getaddress], 200);
    }

    public function addaddress(Request $request)
    {
        // if ($request->lat == "") {
        //     return Redirect::back()->with('danger', trans("messages.select_address"));
        // }
        // if ($request->lang == "") {
        //     return Redirect::back()->with('danger', trans("messages.select_address"));
        // }
        if ($request->city == "") {
            return Redirect::back()->with('danger', 'City is required');
        }
        if ($request->state == "") {
            return Redirect::back()->with('danger', 'State is required');
        }

        if ($request->country == "") {
            return Redirect::back()->with('danger', 'Country is required');
        }

        $pincode=Pincode::select('pincode','delivery_charge')->where('pincode',$request->pincode)
        ->get()->first();

        // if(@$pincode['pincode'] == $request->pincode) {
            try {

                $address = new Address;
                $address->user_id = Session::get('id');
                $address->address_type = $request->address_type;
                $address->address = $request->address;
                $address->lat = $request->lat;
                $address->lang = $request->lang;
                $address->city = $request->city;
                $address->state = $request->state;
                $address->country = $request->country;
                $address->landmark = $request->landmark;
                $address->building = $request->building;
                
                // $address->delivery_charge =$pincode->delivery_charge;
                $address->save();

                return Redirect::back()->with('success', trans("messages.success"));
                
            } catch (Exception $e) {
                $response = $e->getMessage() ;
                return Redirect::back()->with('danger', trans("messages.wrong"));
            }
        // } else {
        //     return Redirect::back()->with('danger', trans("messages.delivery_unavailable"));
        // }
    }

    public function editaddress(Request $request)
    {
        if ($request->city == "") {
            return Redirect::back()->with('danger', 'City is required');
        }
        if ($request->state == "") {
            return Redirect::back()->with('danger', 'State is required');
        }

        if ($request->country == "") {
            return Redirect::back()->with('danger', 'Country is required');
        }
        
        $validation = Validator::make($request->all(),[
            'address_type' => 'required',
            'address' => 'required',
            'landmark' =>'required',
            'building' =>'required',
            
        ]);
        if ($validation->fails())
        {
            return Redirect::back()->withErrors($validation, 'login')->withInput();
        }
        else
        {
            // $pincode=Pincode::select('pincode','delivery_charge')->where('pincode',$request->pincode)
            // ->get()->first();

            // if(@$pincode['pincode'] == $request->pincode) {

                try {

                    Address::where('id', $request->id)->update(['address_type'=>$request->address_type,'address'=>$request->address,'lat'=>$request->lat,'lang'=>$request->lang,'city'=>$request->city,'state'=>$request->state,'country'=>$request->country,'landmark'=>$request->landmark,'building'=>$request->building,'pincode'=>$request->pincode]);


                    return Redirect::back()->with('success', trans("messages.success"));
                    
                } catch (Exception $e) {
                    $response = $e->getMessage() ;
                    return Redirect::back()->with('danger', trans("messages.wrong"));
                }
            // } else {
            //     return Redirect::back()->with('danger', trans("messages.delivery_unavailable"));
            // }

        }
        return Redirect::back()->with('danger', trans('messages.wrong'));
    }

    public function delete(Request $request)
    {
        $address=Address::where('id', $request->id)->where('user_id', $request->user_id)->delete();
        if ($address) {
            return 1;
        } else {
            return 0;
        }
    }

    public function logout() {
        Session::flush ();
        Auth::logout ();
        return Redirect::to('/');
    }
}
