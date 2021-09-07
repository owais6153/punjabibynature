<?php

namespace App\Http\Controllers\admin;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Time;
use Validator;
class TimeController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $gettime = Time::all();
        return view('time',compact('gettime'));
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
        $day = $request->day;
        $open_time = $request->open_time;
        $close_time= $request->close_time;     
        $always_close= $request->always_close;

        foreach($day as $key => $no)
        {
            $input['day'] = $no;
            if ($always_close[$key] == "2") {
                $input['open_time'] = $open_time[$key];
            } else {
                $input['open_time'] = "12:00am";
            }
            if ($always_close[$key] == "2") {
                $input['close_time'] = $close_time[$key];
            } else {
                $input['close_time'] = "11:30pm";
            }
            $input['always_close'] = $always_close[$key];

            Time::where('day', $no)->update($input);

        }

        return redirect()->back()
                        ->with('success_message', 'The time has been updated.');
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show(Request $request)
    {
        $category = Time::findorFail($request->id);
        $getcategory = Time::where('id',$request->id)->first();
        if($getcategory->image){
            $getcategory->image=url('public/images/category/'.$getcategory->image);
        }
        return response()->json(['ResponseCode' => 1, 'ResponseText' => 'Category fetch successfully', 'ResponseData' => $getcategory], 200);
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

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy(Request $request)
    {
       
    }
}
