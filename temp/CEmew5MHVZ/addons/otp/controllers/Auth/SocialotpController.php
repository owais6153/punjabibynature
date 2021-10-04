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
use App\OTPConfiguration;
use Twilio\Rest\Client;

class SocialotpController extends Controller
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
    public function handleFacebookCallback(SocialFacebookAccountService $service)
    {
        if (!$request->has('code') || $request->has('denied')) {
            return redirect('/signin');
        }
        $user = Socialite::driver('facebook')->user();

        $userfacebook=User::where('facebook_id',$user->getId())->first();

        $checkuser=User::where('email','=',$user->email)->where('login_type','!=','facebook')->first();

        if (!empty($checkuser)) {
            return Redirect::to('/signin')->with('danger', trans('messages.email_exist'));
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
                // return Redirect::to('/signup')->with('danger', 'Please add your mobile number');
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
                            'cart' => $cart,
                        ] );

                        return Redirect::to('/');
                    } else {
                        return Redirect::back()->with('danger', trans('messages.blocked'));
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
                    $otp_data['otp'] = $otp;
                    $update=User::where('mobile',$usergoogle->mobile)->update($otp_data);

                    session ( [
                        'mobile' => $usergoogle->mobile,
                    ] );
                    return Redirect::to('/otp-verify')->with('success', trans('messages.email_sent')); 
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