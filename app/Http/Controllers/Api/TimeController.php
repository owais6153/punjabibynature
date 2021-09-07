<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Time;
use App\User;
use Illuminate\Support\Facades\DB;
use Validator;
use DateTime;

class TimeController extends Controller
{
    public function isopenclose(Request $request)
    {
        $getdata=User::select('timezone')->where('type','1')->first();
        date_default_timezone_set($getdata->timezone);

        $date = date('Y/m/d h:i:sa');
        $day = date('l', strtotime($date));

        $isopenclose=Time::where('day','=',$day)->first();

        $current_time = DateTime::createFromFormat('H:i a', date("h:i a"));
        $open_time = DateTime::createFromFormat('H:i a', $isopenclose->open_time);
        $close_time = DateTime::createFromFormat('H:i a', $isopenclose->close_time);

        if ($current_time > $open_time && $current_time < $close_time && $isopenclose->always_close == "2") {
           return response()->json(['status'=>1,'message'=>trans('messages.restaurant_open')],200);
        } else {
           return response()->json(['status'=>0,'message'=>trans('messages.restaurant_closed')],200);
        }
    }
}
