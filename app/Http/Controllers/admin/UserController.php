<?php

namespace App\Http\Controllers\admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use App\User;
use App\Order;
use App\OrderDetails;
use App\Transaction;
use App\Ratting;
use Validator;

class UserController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $getusers = User::where('type', '=' , '2')->get();
        return view('users',compact('getusers'));
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
    public function userdetails(Request $request)
    {
        $getusers = User::where('id',$request->id)->first();

        $getorders = Order::with('users')->select('order.*','users.name')->leftJoin('users', 'order.driver_id', '=', 'users.id')->where('order.user_id',$request->id)->get();
        $getdriver = User::where('type','3')->get();

        return view('user-details',compact('getusers','getorders','getdriver'));
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
    public function update(Request $request, $id)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */

    public function status(Request $request)
    {
        $users = User::where('id', $request->id)->update( array('is_available'=>$request->status) );
        if ($users) {
            return 1;
        } else {
            return 0;
        }
    }

    public function addmoney(Request $request)
    {
        $getuserdata=User::where('id',$request->user_id)->first(); 

        try {

            $wallet = $getuserdata->wallet + $request->amount;

            $UpdateWalletDetails = User::where('id', $request->user_id)
            ->update(['wallet' => $wallet]);

            $Wallet = new Transaction;
            $Wallet->user_id = $request->user_id;
            $Wallet->order_id = NULL;
            $Wallet->order_number = NULL;
            $Wallet->wallet = $request->amount;
            $Wallet->payment_id = NULL;
            $Wallet->order_type = 3;
            $Wallet->transaction_type = '5';
            $Wallet->save();
            if ($Wallet) {
                return 1;
            } else {
                return 0;
            }

        } catch (\Exception $e) {
            return 0;
        }
    }

    public function deductmoney(Request $request)
    {
        $getuserdata=User::where('id',$request->user_id)->first(); 

        try {

            $wallet = $getuserdata->wallet - $request->d_amount;

            if ($wallet >= 0) {
                $UpdateWalletDetails = User::where('id', $request->user_id)
                ->update(['wallet' => $wallet]);

                $Wallet = new Transaction;
                $Wallet->user_id = $request->user_id;
                $Wallet->order_id = NULL;
                $Wallet->order_number = NULL;
                $Wallet->wallet = $request->d_amount;
                $Wallet->payment_id = NULL;
                $Wallet->order_type = 3;
                $Wallet->transaction_type = '6';
                $Wallet->save();
                if ($Wallet) {
                    return 1;
                } else {
                    return 0;
                }
            } else {
                return 2;
            }

        } catch (\Exception $e) {
            return 0;
        }
    }
}
