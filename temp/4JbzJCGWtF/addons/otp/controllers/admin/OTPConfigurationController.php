<?php

namespace App\Http\Controllers\admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Redirect;
use App\OTPConfiguration;
use App\About;
use Validator;

class OTPConfigurationController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $twilioconfigurations = OTPConfiguration::where('name','twilio')->first();
        $msg91configurations = OTPConfiguration::where('name','msg91')->first();
        return view('otp-settings',compact('twilioconfigurations','msg91configurations'));
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
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        //
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
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
        $payment = new OTPConfiguration;
        $payment->exists = true;
        $payment->id = $request->id;

        $payment->twilio_sid =$request->twilio_sid;
        $payment->twilio_auth_token =$request->twilio_auth_token;
        $payment->twilio_mobile_number =$request->twilio_mobile_number;
        $payment->msg_authkey =$request->msg_authkey;
        $payment->msg_template_id =$request->msg_template_id;
        $payment->status =$request->status;
        $payment->save();

        return Redirect::to('/admin/otp-configuration')->with('success', "Details has been updated!");
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
