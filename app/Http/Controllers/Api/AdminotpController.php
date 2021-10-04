<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\DB;
use App\User;
use App\Order;
use App\OrderDetails;
use App\Addons;
use App\About;
use App\Transaction;
use Validator;
use Carbon\Carbon;

class AdminotpController extends Controller
{
  public function login(Request $request )
  {
    if($request->email == ""){
        return response()->json(["status"=>0,"message"=>trans('messages.email_required')],400);
    }
    if($request->password == ""){
        return response()->json(["status"=>0,"message"=>trans('messages.password_required')],400);
    }
    
    $login=User::where('email',$request->email)->where('type','=','1')->first();

    if(!empty($login))
    {
      if(Hash::check($request->get('password'),$login->password))
      {
          $arrayName = array(
              'id' => $login->id,
              'name' => $login->name,
              'email' => $login->email,
              'mobile' => $login->mobile,
              'profile_image' => url('/storage/app/public/images/profile/'.$login->profile_image),
          );

          $data=array('user'=>$arrayName);
          $status=1;
          $message='Success';

          $data_token['token'] = $request->token;
          $update=User::where('email',$request->email)->update($data_token);

          return response()->json(['status'=>$status,'message'=>$message,'data'=>$arrayName],200);
      }
      else
      {
          $status=0;
          $message=trans('messages.invalid_password');
          return response()->json(['status'=>$status,'message'=>$message],422);
      }
    } else {
      $status=0;
      $message=trans('messages.invalid_email');
      $data="";
      return response()->json(['status'=>$status,'message'=>$message],422);
    }
  }

  public function home(Request $request)
  {
    if($request->user_id == 1){
      $getdata=User::select('max_order_qty','min_order_amount','max_order_amount','currency','firebase','map','referral_amount')->where('type','1')
          ->get()->first();
      $todayorders = Order::with('users')->select('order.order_total as total_price','order.id','order.order_number','order.status','order.payment_type','order.order_type',DB::raw('DATE_FORMAT(order.created_at, "%d-%m-%Y") as date'))
      ->leftJoin('users', 'order.driver_id', '=', 'users.id')
      ->where('order.created_at','LIKE','%' .date("Y-m-d") . '%')->orderBy('order.id', 'DESC')->get();

      if (count($todayorders) == 0) {
        $torder = [];
      } else {
        $torder = $todayorders;
      }

      $earning = Order::where('status','!=','5')->where('status','!=','6')->sum('order_total');
      $order_tax = Order::where('status','!=','5')->where('status','!=','6')->sum('tax_amount');
      $total_orders = Order::all();
      $latest_order = Order::where('created_at','LIKE','%' .date("Y-m-d") . '%')->count('id');
      $cancelled_order = Order::whereIn('status', ['5', '6'])->get();

      if ($todayorders) {
        return response()->json(['status'=>1,'message'=>'Success','earning'=>number_format($earning-$order_tax,2),'currency'=>$getdata->currency,'total_orders'=>count($total_orders),'cancelled_order'=>count($cancelled_order),'latest_order'=>$latest_order,'todayorders'=>$torder],200);
      } else {
        return response()->json(['status'=>0,'message'=>trans('messages.no_data')],422);
      }
    } else {
      return response()->json(["status"=>0,"message"=>"Invalid User ID"],400);
    }    
  }

  public function history(Request $request)
  {
    if($request->user_id == 1){

      if ($request->start_date && $request->end_date) {

        $orders = Order::with('users')
        ->select('order.order_total as total_price','order.id','order.order_number','order.status','order.payment_type','order.order_type',DB::raw('DATE_FORMAT(order.created_at, "%d-%m-%Y") as date'))
        ->leftJoin('users', 'order.driver_id', '=', 'users.id')
        ->whereBetween('order.created_at', [new Carbon($request->start_date."00:00:00"), new Carbon($request->end_date."23:59:59")])
        ->get();
      } else {
      $orders = OrderDetails::select('order.order_total as total_price','order.id','order.order_number','order.status','order.payment_type','order.order_type',DB::raw('DATE_FORMAT(order.created_at, "%d-%m-%Y") as date'))
          ->join('item','order_details.item_id','=','item.id')
          ->join('order','order_details.order_id','=','order.id')
          ->groupBy('order_details.order_id')->orderBy('order.created_at','DESC')->get();
      }      

      if ($orders) {
        return response()->json(['status'=>1,'message'=>'Success','orders'=>$orders],200);
      } else {
        return response()->json(['status'=>0,'message'=>trans('messages.no_data')],422);
      }
    } else {
      return response()->json(["status"=>0,"message"=>trans('messages.wrong')],400);
    }    
  }

  public function orderdetails(Request $request)
  {
    if($request->order_id == ""){
        return response()->json(["status"=>0,"message"=>trans('messages.order_number')],400);
    }
    if($request->user_id == 1){

        $cartdata=OrderDetails::with('itemimage')->select('order_details.id','order_details.qty','order_details.price as total_price','item.id','order_details.item_name',\DB::raw("CONCAT('".url('/storage/app/public/images/item/')."/', order_details.item_image) AS item_image"),'order_details.variation','order_details.variation_price','order_details.item_id','order_details.addons_id','order_details.addons_name','order_details.addons_price','order_details.item_notes')
        ->join('item','order_details.item_id','=','item.id')
        ->join('order','order_details.order_id','=','order.id')
        ->where('order_details.order_id',$request->order_id)->get()->toArray();
        
        $status=Order::select('order.driver_id','order.user_id','order.address','order.building','order.landmark','order.pincode','order.promocode','order.discount_amount','order.order_number','order.status','order.order_notes','order.order_type','order.tax','order.lat','order.lang','order.delivery_charge','users.name',\DB::raw("CONCAT('".url('/storage/app/public/images/profile/')."/', users.profile_image) AS profile_image"),'users.mobile','order.tax_amount')
        ->join('users','order.user_id','=','users.id')
        ->where('order.id',$request['order_id'])
        ->get()->first();

        $getdriver=User::select('name',\DB::raw("CONCAT('".url('/storage/app/public/images/profile/')."/', profile_image) AS profile_image"),'mobile')->where('id',$status->driver_id)
        ->get()->first();

        $getusers=User::select('name',\DB::raw("CONCAT('".url('/storage/app/public/images/profile/')."/', profile_image) AS profile_image"),'mobile','email')->where('id',$status->user_id)
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

        if (@$getusers["name"] == "") {
            $username = "";
            $userprofile_image = "";
            $usermobile = "";
            $useremail = "";
        } else {
            $username = $getusers["name"];
            $userprofile_image = $getusers["profile_image"];
            $usermobile = $getusers["mobile"];
            $useremail = $getusers["email"];
        }

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
            'order_status' => $status->status,
            'delivery_charge' => "$status->delivery_charge",
            'driver_name' => $drivername,
            'driver_profile_image' => $driverprofile_image,
            'driver_mobile' => $drivermobile,
            'user_name' => $username,
            'user_profile_image' => $userprofile_image,
            'user_mobile' => $usermobile,
            'user_email' => $useremail
        );
        
        if(!empty($cartdata))
        {
            return response()->json(['status'=>1,'message'=>'success','delivery_address'=>$status->address,'landmark'=>$status->landmark,'building'=>$status->building,'pincode'=>$status->pincode,'order_type'=>$status->order_type,'order_number'=>$status->order_number,'name'=>$status->name,'profile_image'=>$status->profile_image,'mobile'=>$status->mobile,'lat'=>$status->lat,'lang'=>$status->lang,'data'=>@$cdata,'summery'=>$summery],200);
        }
        else
        {
            return response()->json(['status'=>0,'message'=>trans('messages.no_data')],200);
        }
    } else {
        return response()->json(["status"=>0,"message"=>trans('messages.wrong')],400);
    }    
  }

  public function update(Request $request)
  {
      $UpdateDetails = Order::where('id', $request->order_id)
                  ->update(['status' => $request->status]);
      $getdata=User::select('token','firebase')->where('type','1')->first();

      if ($UpdateDetails) {
        //Notification
        $userdetails = Order::where('id', $request->order_id)->first();

        
        $getalluses=User::select('token','email','name','wallet')->where('id',$userdetails->user_id)
        ->get()->first();

        $getadmin=User::select('token','email','firebase')->where('type','1')->first();

        if ($request->status == "6") {

            if ($userdetails->payment_type != "0") {

                $wallet = $getalluses->wallet + $userdetails->order_total;

                $UpdateWalletDetails = User::where('id', $userdetails->user_id)
                ->update(['wallet' => $wallet]);

                $Wallet = new Transaction;
                $Wallet->user_id = $userdetails->user_id;
                $Wallet->order_id = $userdetails->order_id;
                $Wallet->order_number = $userdetails->order_number;
                $Wallet->wallet = $userdetails->order_total;
                $Wallet->payment_id = $userdetails->razorpay_payment_id;
                $Wallet->order_type = $userdetails->order_type;
                $Wallet->transaction_type = '1';
                $Wallet->save();

            }

            $usertitle = "Order";
            $userbody = 'Order '.$userdetails->order_number.' has been cancelled by admin';
            
            $usergoogle_api_key = $getadmin->firebase; 
            
            $userregistrationIds = $getalluses->token;
            #prep the bundle
            $usermsg = array
                (
                'body'  => $userbody,
                'title' => $usertitle,
                'sound' => 1/*Default sound*/
                );
            $userfields = array
                (
                'to'            => $userregistrationIds,
                'notification'  => $usermsg
                );
            $useradminheaders = array
                (
                'Authorization: key=' . $usergoogle_api_key,
                'Content-Type: application/json'
                );
            #Send Reponse To FireBase Server
            $userch = curl_init();
            curl_setopt( $userch,CURLOPT_URL, 'https://fcm.googleapis.com/fcm/send' );
            curl_setopt( $userch,CURLOPT_POST, true );
            curl_setopt( $userch,CURLOPT_HTTPHEADER, $useradminheaders );
            curl_setopt( $userch,CURLOPT_RETURNTRANSFER, true );
            curl_setopt( $userch,CURLOPT_SSL_VERIFYPEER, false );
            curl_setopt( $userch,CURLOPT_POSTFIELDS, json_encode( $userfields ) );

            $result = curl_exec ( $userch );
            curl_close ( $userch );

            $admintitle = "Order";
            $adminbody = 'Order '.$userdetails->order_number.' has been cancelled by you';
            $admingoogle_api_key = $getadmin->firebase; 
            
            $adminregistrationIds = $getadmin->token;
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
        }

        $title = "Order";

        if ($request->status == "2") {
            $body = 'Your Order '.$userdetails->order_number.' is accepted';
            
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
        } elseif ($request->status == "4") {
            $body = 'Your Order '.$userdetails->order_number.' is delivered';
            
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
        }
        
        return response()->json(['status'=>1,'message'=>'Success'],200);
      } else {
          return response()->json(['status'=>0,'message'=>trans('messages.no_data')],200);
      }
  }

  public function drivers()
  {
    $getdriver = User::select('id','name','email','mobile',\DB::raw("CONCAT('".url('/storage/app/public/images/profile/')."/', profile_image) AS image"))->where('type','3')->where('is_available','1')->get();
    if ($getdriver) {
      return response()->json(['status'=>1,'message'=>'Success','data'=>$getdriver],200);
    } else {
      return response()->json(['status'=>1,'message'=>trans('messages.no_data')],200);
    }
  }

  public function assign(Request $request)
  {
      $UpdateDetails = Order::where('id', $request->order_id)
                  ->update(['driver_id' => $request->driver_id,'status' => '3']);

      $userdetails = Order::where('id', $request->order_id)->first();

      $getdata=User::select('firebase')->where('type','1')->first();

      $google_api_key = $getdata->firebase;

      $title = "Order";

      if ($userdetails->driver_id) {

          $gettoken=User::select('token','name','email')->where('id',$userdetails->driver_id)
          ->get()->first();

          $body = 'New Order '.$userdetails->order_number.' assigned to you';

          $registrationIds = $gettoken->token;
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
      }

      if ($userdetails->user_id) {
          $gettoken=User::select('token','name','email')->where('id',$userdetails->user_id)
          ->get()->first();

          $body = 'Your Order '.$userdetails->order_number.' is on the way';

          $registrationIds = $gettoken->token;
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
      }

      if ($UpdateDetails) {
          return response()->json(['status'=>1,'message'=>'Success'],200);
      } else {
          return response()->json(['status'=>0,'message'=>trans('messages.no_data')],200);
      }
  }

  public function updateprofile(Request $request)
  {
    if($request->user_id == ""){
        return response()->json(["status"=>0,"message"=>trans('messages.user_required')],400);
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
    $user->email =$request->email;
    $user->mobile =$request->mobile;
    $user->save();

    $user_details=User::where('id',$request->user_id)->where('type','=','1')->first();
    $arrayName = array(
      'mobile' => $user_details->mobile,
      'email' => $user_details->email,
      'profile_image' => url('/storage/app/public/images/profile/'.$user_details->profile_image),
    );

    if($user)
    {
      return response()->json(['status'=>1,'message'=>trans('messages.update'),'data'=>$arrayName],200);
    }
    else
    {
      return response()->json(['status'=>0,'message'=>trans('messages.wrong')],400);
    }
  }

  public function updatepassword(Request $request)
  {
      if($request->user_id == ""){
          return response()->json(["status"=>0,"message"=>trans('messages.user_required')],400);
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

}
