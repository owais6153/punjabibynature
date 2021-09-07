<?php

namespace App\Http\Controllers\admin;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Notification;
use App\User;
use Validator;
use Auth;

class NotificationController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $getnotification = Notification::all();
        return view('notification',compact('getnotification'));
    }

    public function list()
    {
        $getnotification = Notification::all();
        return view('theme.notificationtable',compact('getnotification'));
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $s
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        $validation = Validator::make($request->all(),[
          'title' => 'required',
          'message' => 'required',
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
            $notification = new Notification;
            $notification->title =$request->title;
            $notification->message =$request->message;
            $notification->save();

            //Driver Notification
            $getallusers=User::select('token')->where('type','2')->where('is_available','1')->groupBy('token')->get();

            $google_api_key = Auth::user()->firebase; 

            foreach ($getallusers as $val) {
                $notititle = $request->title;
                $notimessage = $request->message;
                
                $userregistrationIds = $val['token'];
                #prep the bundle
                $usermsg = array
                    (
                    'body'  => $notimessage,
                    'title' => $notititle,
                    'sound' => 1/*Default sound*/
                    );
                $userfields = array
                    (
                    'to'            => $userregistrationIds,
                    'notification'  => $usermsg
                    );
                $userheaders = array
                    (
                    'Authorization: key=' . $google_api_key,
                    'Content-Type: application/json'
                    );
                #Send Reponse To FireBase Server
                $dch = curl_init();
                curl_setopt( $dch,CURLOPT_URL, 'https://fcm.googleapis.com/fcm/send' );
                curl_setopt( $dch,CURLOPT_POST, true );
                curl_setopt( $dch,CURLOPT_HTTPHEADER, $userheaders );
                curl_setopt( $dch,CURLOPT_RETURNTRANSFER, true );
                curl_setopt( $dch,CURLOPT_SSL_VERIFYPEER, false );
                curl_setopt( $dch,CURLOPT_POSTFIELDS, json_encode( $userfields ) );

                $userresult = curl_exec ( $dch );
                
                curl_close ( $dch );
            }
            $success_output = 'Notification has been sent!';
        }
        $output = array(
            'error'     =>  $error_array,
            'success'   =>  $success_output
        );
        echo json_encode($output);
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show(Request $request)
    {
       
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit(Request $req)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request)
    {

        
    }

    public function updateorder(Request $request)
    {
        
    }
    
    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function status(Request $request)
    {
        
    }
}
