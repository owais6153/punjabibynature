<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Socialite;
use App\Services\SocialFacebookAccountService;

use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Redirect;
use Session;
use Auth;
use App\User;
use App\About;
use App\Cart;

class SocialController extends Controller
{


    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function redirectToFacebook()
    {
        return Socialite::driver('facebook')->redirect();
    }


    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function handleFacebookCallback(SocialFacebookAccountService $service, Request $request)
    {
        if (!$request->has('code') || $request->has('denied')) {
            return redirect('/signin');
        }
        $user = Socialite::driver('facebook')->user();

        $userfacebook=User::where('facebook_id',$user->getId())->first();

        $checkuser=User::where('email','=',$user->email)->where('login_type','!=','facebook')->first();

        if (!empty($checkuser)) {
            return Redirect::to('/signin')->with('danger', 'Email id Already exist');
        }

        $otp = rand ( 100000 , 999999 );
        if ($userfacebook != "" OR @$userfacebook->email == $user->getEmail() AND $user->getEmail() != "") {
            if ($userfacebook->mobile == "") {
                session ( [
                    'name' => $user->getName(),
                    'email' => $user->getEmail(),
                    'facebook_id' => $user->getId(),
                ] );
                return Redirect::to('/signup');
            } else {
                
                if($userfacebook->is_verified == '1') 
                {
                    if($userfacebook->is_available == '1') {
                        // Check item in Cart
                        $cart=Cart::where('user_id',$userfacebook->id)->count();
                        $getdata=User::select('referral_amount')->where('type','1')->first();

                        session ( [ 
                            'id' => $userfacebook->id, 
                            'name' => $userfacebook->name,
                            'referral_code' => $userfacebook->referral_code,
                            'referral_amount' => $getdata->referral_amount,
                            'email' => $userfacebook->email,
                            'mobile' => $userfacebook->mobile,
                            'profile_image' => $userfacebook->profile_image,
                            'login_type' => 'facebook',
                            'cart' => $cart,
                        ] );

                        return Redirect::to('/');
                    } else {
                        return Redirect::back()->with('danger', 'Your account has been blocked by Admin');
                    }
                } else {
                                    
                    $getlogo = About::select('logo')->where('id','=','1')->first();

                    $title=trans('messages.email_code');
                    $email=$request->email;
                    $logo=$getlogo->logo;
                    $data=['title'=>$title,'email'=>$email,'otp'=>$otp,'logo'=>$logo];

                    Mail::send('Email.emailverification',$data,function($message)use($data){
                        $message->from(env('MAIL_USERNAME'))->subject($data['title']);
                        $message->to($data['email']);
                    } );

                    $otp_data['otp'] = $otp;
                    $update=User::where('email',$userfacebook->email)->update($otp_data);

                    session ( [
                        'email' => $userfacebook->email,
                    ] );
                    return Redirect::to('/email-verify')->with('success', 'Email has been sent to your registered email address'); 
                }
            }
        } else {

            session ( [
                'name' => $user->getName(),
                'email' => $user->getEmail(),
                'facebook_id' => $user->getId(),
            ] );
            return Redirect::to('/signup');

        }
    }
}