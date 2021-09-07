<?php

namespace App\Http\Controllers\admin;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Hash;
use App\User;
use Validator;
class DriverController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $getdriver = User::where('type','3')->get();
        return view('driver',compact('getdriver'));
    }

    public function list()
    {
        $getdriver = User::where('type','3')->get();
        return view('theme.drivertable',compact('getdriver'));
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
          'name' => 'required',
          'email' => 'required|unique:users',
          'mobile' => 'required|unique:users',
          'password' => 'required',
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
            $driver = new User;
            $driver->name = $request->name;
            $driver->email = $request->email;
            $driver->mobile = $request->mobile;
            $driver->profile_image = "unknown.png";
            $driver->type = "3";
            $driver->password = Hash::make($request->password);
            $driver->save();
            $success_output = 'Driver Added Successfully!';
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
        $driver = User::findorFail($request->id);
        $getdriver = User::select('id','name','email','mobile')->where('id',$request->id)->first();

        return response()->json(['ResponseCode' => 1, 'ResponseText' => 'driver fetch successfully', 'ResponseData' => $getdriver], 200);
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

        $validation = Validator::make($request->all(),[
          'name' => 'required',
          'email' => 'required|unique:users,name,' . $request->id,
          'mobile' => 'required|unique:users,mobile,' . $request->id
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
            $driver = new User;
            $driver->exists = true;
            $driver->id = $request->id;
            $driver->name =$request->name;
            $driver->email =$request->email;
            $driver->mobile =$request->mobile;
            $driver->save();           

            $success_output = 'Driver updated Successfully!';
        }
        $output = array(
            'error'     =>  $error_array,
            'success'   =>  $success_output
        );
        echo json_encode($output);
    }

    public function status(Request $request)
    {
        $users = User::where('id', $request->id)->update( array('is_available'=>$request->status) );
        if ($users) {
            return 1;
        } else {
            return 0;
        }
    }
}
