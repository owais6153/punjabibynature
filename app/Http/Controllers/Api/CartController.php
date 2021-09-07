<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Cart;
use App\Item;
use App\Addons;
use App\ItemImages;
use App\User;
use Illuminate\Support\Facades\DB;
use Validator;

class CartController extends Controller
{
    public function cart(Request $request)
    {
      if($request->item_id == ""){
          return response()->json(["status"=>0,"message"=>trans('messages.item_required')],400);
      }
      if($request->qty == ""){
          return response()->json(["status"=>0,"message"=>trans('messages.qty_required')],400);
      }
      if($request->price == ""){
          return response()->json(["status"=>0,"message"=>trans('messages.price_required')],400);
      }
      if($request->user_id == ""){
          return response()->json(["status"=>0,"message"=>trans('messages.user_required')],400);
      }
      if($request->variation_id == ""){
          return response()->json(["status"=>0,"message"=>trans('messages.variation_required')],400);
      }

      try {
        $getitem=Item::with('itemimage')->select('item.id','item.item_name','item.tax')
        ->where('item.id',$request->item_id)->first();

        $cart = new Cart;
        $cart->item_id =$request->item_id;
        $cart->addons_id =$request->addons_id;
        $cart->qty =$request->qty;
        $cart->price =$request->price;
        $cart->variation_id =$request->variation_id;
        $cart->variation_price =$request->variation_price;
        $cart->variation =$request->variation;
        $cart->user_id =$request->user_id;
        $cart->item_notes =$request->item_notes;
        $cart->item_name =$getitem->item_name;
        $cart->tax =$getitem->tax;
        $cart->item_image =$getitem['itemimage']->image_name;
        $cart->addons_name =$request->addons_name;
        $cart->addons_price =$request->addons_price;
        $cart->save();

        return response()->json(['status'=>1,'message'=>trans('messages.added_in_cart')],200);

        } catch (\Exception $e){

            return response()->json(['status'=>0,'message'=>trans('messages.wrong')],400);
        }
    }

   	public function getcart(Request $request)
   	{
   	    if($request->user_id == ""){
   	        return response()->json(["status"=>0,"message"=>trans('messages.user_required')],400);
   	    }

   	    $cartdata=Cart::select('id','qty','price','item_notes','cart.variation','item_name','tax',\DB::raw("CONCAT('".url('/storage/app/public/images/item/')."/', item_image) AS item_image"),'item_id','addons_id','addons_name','addons_price')
        ->where('is_available','1')
   	    ->where('user_id',$request->user_id)->get()->toArray();

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

          $data[] = array(
              'id' => $value['id'],
              'qty' => $value['qty'],
              'price' => $value['price'],
              'item_notes' => $item_notes,
              'item_name' => $value['item_name'],
              'addons_price' => $addons_price,
              'item_image' => $value['item_image'],
              'addons_name' => $addons_name,
              'item_id' => $value['item_id'],
              'variation' => $value['variation'],
              'addons_id' => $addons_id
          );
        }

   	    if(!empty($cartdata))
   	    {
   	        return response()->json(['status'=>1,'message'=>'Cart Data Successful','data'=>$data],200);
   	    }
   	    else
   	    {
   	        return response()->json(['status'=>0,'message'=>trans('messages.no_data')],200);
   	    }
   	}

   	public function qtyupdate(Request $request)
   	{
      if($request->cart_id == ""){
          return response()->json(["status"=>0,"message"=>trans('messages.cart_id_required')],400);
      }
      if($request->item_id == ""){
          return response()->json(["status"=>0,"message"=>trans('messages.item_required')],400);
      }
      if($request->qty == ""){
          return response()->json(["status"=>0,"message"=>trans('messages.qty_required')],400);
      }
      if($request->user_id == ""){            
       return response()->json(["status"=>0,"message"=>trans('messages.user_required')],400);
      }

      $data=Item::where('item.id', $request['item_id'])
      ->get()
      ->first();

      $cartdata=Cart::where('cart.id', $request['cart_id'])
      ->get()
      ->first();

      $getdata=User::select('max_order_qty','min_order_amount','max_order_amount')->where('type','1')
      ->get()->first();

      if ($getdata->max_order_qty < $request->qty) {
        return response()->json(['status'=>0,'message'=>trans('messages.maximum_purchase')],200);
      }

      $arr = explode(',', $cartdata->addons_id);
      $d = Addons::whereIn('id',$arr)->get();

      $sum = 0;
      foreach($d as $key => $value) {
          $sum += $value->price; 
      }

      if ($request->type == "decreaseValue") {
          $qty = $cartdata->qty-1;
      } else {
          $qty = $cartdata->qty+1;
      }

      $update=Cart::where('id',$request['cart_id'])->update(['item_id'=>$request->item_id,'qty'=>$qty]);

      return response()->json(['status'=>1,'message'=>trans('messages.qty_update')],200);
   	}

    public function deletecartitem(Request $request)
    {
        if($request->cart_id == ""){
            return response()->json(["status"=>0,"message"=>trans('messages.cart_id_required')],400);
        }

        $cart=Cart::where('id', $request->cart_id)->delete();
        if($cart)
        {
            return response()->json(['status'=>1,'message'=>trans('messages.delete')],200);
        }
        else
        {
            return response()->json(['status'=>0,'message'=>trans('messages.wrong')],200);
        }
    }

    public function cartcount(Request $request)
    {
        if($request->user_id == ""){
            return response()->json(["status"=>0,"message"=>trans('messages.user_required')],400);
        }

        $cartdata=Cart::where('user_id',$request->user_id)->where('is_available','1')->count();

        if(!empty($cartdata))
        {
            return response()->json(['status'=>1,'message'=>'Cart Data Successful','cart'=>$cartdata],200);
        }
        else
        {
            return response()->json(['status'=>0,'message'=>trans('messages.no_data')],200);
        }
    }
}