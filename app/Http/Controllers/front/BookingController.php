<?php

namespace App\Http\Controllers\front;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Booking;
use App\BookingDates;

class BookingController extends Controller
{
    public function (Request $request){
        if ($request->booking_date == '') {
           return response()->json(["status"=>0,"message"=>"Booking date required"],400);
        }

        $checkBooking = BookingDates::where('booking_date', '=', $request->booking_date )->first();
        if (!empty($checkBooking)) {
           return response()->json(["status"=>0,"message"=>"Sorry! we're already booked on this date."],400);
        }

           return response()->json(["status"=>0,"message"=>"Booking date required"],400);


    }
}
