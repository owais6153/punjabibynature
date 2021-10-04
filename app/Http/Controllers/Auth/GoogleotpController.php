<?php
  
namespace App\Http\Controllers\Auth;
  
use App\Http\Controllers\Controller;
use Socialite;
use Auth;
use Exception;
use App\User;
use App\About;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Redirect;
use Session;
use App\Cart;
use App\OTPConfiguration;
use Twilio\Rest\Client;
  
class GoogleotpController extends Controller
{
    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function redirectToGoogle()
    {
        return Socialite::driver('google')->redirect();
    }
      
    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function handleGoogleCallback()
    {
        try {
    
            $user = Socialite::driver('google')->user();

            $usergoogle=User::where('google_id',$user->id)->first();
            $checkuser=User::where('email','=',$user->email)->where('login_type','!=','google')->first();

            if (!empty($checkuser)) {
                return Redirect::to('/signin')->with('danger', trans('messages.email_exist'));
            }

            $otp = rand ( 100000 , 999999 );
            if ($usergoogle != "" OR @$usergoogle->email == $user->email AND $user->email != "") {
                if ($usergoogle->mobile == "") {
                    session ( [
                        'name' => $user->name,
                        'email' => $user->email,
                        'mobile' => $user->mobile,
                        'google_id' => $user->id,
                    ] );
                    return Redirect::to('/signup');
                } else {
                    
                    if($usergoogle->is_verified == '1') 
                    {
                        if($usergoogle->is_available == '1') {
                            // Check item in Cart
                            $cart=Cart::where('user_id',$usergoogle->id)->count();
                            $getdata=User::select('referral_amount')->where('type','1')->first();

                            session ( [ 
                                'id' => $usergoogle->id, 
                                'name' => $usergoogle->name,
                                'email' => $usergoogle->email,
                                'mobile' => $usergoogle->mobile,
                                'referral_code' => $usergoogle->referral_code,
                                'referral_amount' => $getdata->referral_amount,
                                'profile_image' => $usergoogle->profile_image,
                                'cart' => $cart,
                            ] );

                            return Redirect::to('/');
                        } else {
                            return Redirect::back()->with('danger', trans('messages.blocked'));
                        }
                    } else {
                        if (\App\SystemAddons::where('unique_identifier', 'otp')->first() != null && \App\SystemAddons::where('unique_identifier', 'otp')->first()->activated) {

                            $getconfiguration = OTPConfiguration::select('twilio_sid','twilio_auth_token','twilio_mobile_number')->where('id','=','1')->first();

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

                            $otp_data['otp'] = $otp;
                            $update=User::where('mobile',$usergoogle->mobile)->update($otp_data);

                            session ( [
                                'mobile' => $usergoogle->mobile,
                            ] );

                            return Redirect::to('/otp-verify')->with('success', trans('messages.email_sent')); 

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
                }
            } else {

                session ( [
                    'name' => $user->name,
                    'email' => $user->email,
                    'google_id' => $user->id,
                ] );
                return Redirect::to('/signup');

            }
    
        } catch (Exception $e) {
            return Redirect::to('/signin')->with('danger', trans('messages.wrong'));
        }
    }
}