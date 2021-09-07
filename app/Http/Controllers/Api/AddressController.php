<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Address;
use App\Pincode;
use Validator;

class AddressController extends Controller
{
    public function address(Request $request)
    {
        if($request->user_id == ""){
            return response()->json(["status"=>0,"message"=>trans('messages.user_required')],400);
        }
        if($request->address_type == ""){
            return response()->json(["status"=>0,"message"=>trans('messages.select_address_type')],400);
        }
        if($request->address == ""){
            return response()->json(["status"=>0,"message"=>trans('messages.address_required')],400);
        }
        if($request->lat == ""){
            return response()->json(["status"=>0,"message"=>trans('messages.select_address')],400);
        }
        if($request->lang == ""){
            return response()->json(["status"=>0,"message"=>trans('messages.select_address')],400);
        }
        if($request->landmark == ""){
            return response()->json(["status"=>0,"message"=>trans('messages.landmark_required')],400);
        }
        if($request->building == ""){
            return response()->json(["status"=>0,"message"=>trans('messages.building_required')],400);
        }
        if($request->pincode == ""){
            return response()->json(["status"=>0,"message"=>trans('messages.pincode_required')],400);
        }

        $pincode=Pincode::select('pincode','delivery_charge')->where('pincode',$request['pincode'])->first();

        if($pincode['pincode']== $request->pincode) {
            if(!empty($pincode))
            {
                try {
                    $address = new Address;
                    $address->user_id =$request->user_id;
                    $address->address_type =$request->address_type;
                    $address->address =$request->address;
                    $address->lat =$request->lat;
                    $address->lang =$request->lang;
                    $address->landmark =$request->landmark;
                    $address->building =$request->building;
                    $address->pincode =$request->pincode;
                    $address->delivery_charge =$pincode->delivery_charge;
                    $address->save();

                    return response()->json(['status'=>1,'message'=>trans('messages.success')],200);
                } catch (\Exception $e){
                    return response()->json(['status'=>0,'message'=>trans('messages.wrong')],400);
                }
            }
        } else {
            return response()->json(['status'=>0,'message'=>trans('messages.delivery_unavailable')],200);
        }
    }

    public function getaddress(Request $request)
    {
        if($request->user_id == ""){
            return response()->json(["status"=>0,"message"=>trans('messages.user_required')],400);
        }

        try {
            $address=Address::select('id','user_id','address_type','address','lat','lang','landmark','building','pincode','delivery_charge')->where('user_id',$request->user_id)->get();

            return response()->json(['status'=>1,'message'=>'Address','data'=>$address],200);
        } catch (\Exception $e){
            return response()->json(['status'=>0,'message'=>trans('messages.wrong')],400);
        }
    }

    public function updateaddress(Request $request)
    {
        if($request->user_id == ""){
            return response()->json(["status"=>0,"message"=>trans('messages.user_required')],400);
        }

        if($request->address_id == ""){
            return response()->json(["status"=>0,"message"=>trans('messages.address_required')],400);
        }

        $pincode=Pincode::select('pincode')->where('pincode',$request['pincode'])->first();

        if($pincode['pincode']== $request->pincode) {
            if(!empty($pincode))
            {
                try {
                    $data_address['address_type'] = $request->address_type;
                    $data_address['address'] = $request->address;
                    $data_address['lat'] = $request->lat;
                    $data_address['lang'] = $request->lang;
                    $data_address['landmark'] = $request->landmark;
                    $data_address['building'] = $request->building;
                    $data_address['pincode'] = $request->pincode;

                    $update=Address::where('user_id',$request->user_id)->where('id',$request->address_id)->update($data_address);

                    return response()->json(['status'=>1,'message'=>trans('messages.update')],200);
                } catch (\Exception $e){
                    return response()->json(['status'=>0,'message'=>trans('messages.wrong')],400);
                }
            }
        } else {
            return response()->json(['status'=>0,'message'=>trans('messages.delivery_unavailable')],200);
        }
    }

    public function deleteaddress(Request $request)
    {
        if($request->user_id == ""){
            return response()->json(["status"=>0,"message"=>trans('messages.user_required')],400);
        }

        if($request->address_id == ""){
            return response()->json(["status"=>0,"message"=>trans('messages.address_required')],400);
        }

        try {
            $delete=Address::where('user_id',$request->user_id)->where('id', $request->address_id)->delete();

            return response()->json(['status'=>1,'message'=>trans('messages.delete')],200);
        } catch (\Exception $e){
            return response()->json(['status'=>0,'message'=>trans('messages.wrong')],400);
        }
    }
}
