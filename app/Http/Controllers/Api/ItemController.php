<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Item;
use App\Favorite;
use App\User;
use App\ItemImages;
use App\Ingredients;
use App\Addons;
use Illuminate\Support\Facades\DB;
use Validator;

class ItemController extends Controller
{
    public function item(Request $request)
    {
        if($request->cat_id == ""){
            return response()->json(["status"=>0,"message"=>trans('messages.select_category')],400);
        }

        $getdata=User::select('max_order_qty','min_order_amount','max_order_amount','currency','firebase','map','referral_amount')->where('type','1')
            ->get()->first();

        if($request->user_id == ""){
            $user_id  = $request->user_id;
            $itemdata=Item::with(['itemimage','variation'])->select('item.id','item.item_name',DB::raw('(case when favorite.item_id is null then 0 else 1 end) as is_favorite'))
            ->leftJoin('favorite', function($query) use($user_id) {
                $query->on('favorite.item_id','=','item.id')
                ->where('favorite.user_id', '=', $user_id);
            })
            ->join('categories','item.cat_id','=','categories.id')
            ->where('item.item_status','1')
            ->where('item.is_deleted','2')
            ->where('item.cat_id',$request['cat_id'])->orderBy('item.id', 'DESC')->paginate(10);

            if(!empty($itemdata))
            {
                return response()->json(['status'=>1,'message'=>'Item Successful','data'=>$itemdata,'currency'=>$getdata->currency,'max_order_qty'=>$getdata->max_order_qty,'min_order_amount'=>$getdata->min_order_amount,'max_order_amount'=>$getdata->max_order_amount,'map'=>"AIzaSyDgYRuLoA_VsDsX2kgKoc3JiUnVeu085Vo",'referral_amount'=>$getdata->referral_amount],200);
            }
            else
            {
                return response()->json(['status'=>0,'message'=>trans('messages.no_data')],200);
            }
            return response()->json(["status"=>0,"message"=>trans('messages.select_category')],400);
        } else {
            $checkuser=User::where('id',$request->user_id)->first();

            if($checkuser->is_available == '1') 
            {
                $user_id  = $request->user_id;
                $itemdata=Item::with(['itemimage','variation'])->select('item.id','item.item_name',DB::raw('(case when favorite.item_id is null then 0 else 1 end) as is_favorite'))
                ->leftJoin('favorite', function($query) use($user_id) {
                    $query->on('favorite.item_id','=','item.id')
                    ->where('favorite.user_id', '=', $user_id);
                })
                ->join('categories','item.cat_id','=','categories.id')
                ->where('item.item_status','1')
                ->where('item.is_deleted','2')
                ->where('item.cat_id',$request['cat_id'])->orderBy('item.id', 'DESC')->paginate(10);

                if(!empty($itemdata))
                {
                    return response()->json(['status'=>1,'message'=>'Item Successful','data'=>$itemdata,'currency'=>$getdata->currency,'max_order_qty'=>$getdata->max_order_qty,'min_order_amount'=>$getdata->min_order_amount,'max_order_amount'=>$getdata->max_order_amount,'map'=>"AIzaSyDgYRuLoA_VsDsX2kgKoc3JiUnVeu085Vo",'referral_amount'=>$getdata->referral_amount],200);
                }
                else
                {
                    return response()->json(['status'=>0,'message'=>trans('messages.no_data')],200);
                }
                return response()->json(["status"=>0,"message"=>trans('messages.select_category')],400);
            } else {
                $status=2;
                $message=trans('messages.blocked');
                return response()->json(['status'=>$status,'message'=>$message],422);
            }
        }
    }

    public function itemdetails(Request $request)
    {
        if($request->item_id == ""){
            return response()->json(["status"=>0,"message"=>trans('messages.item_required')],400);
        }

    	$itemdata=Item::with(['itemimagedetails','itemimage','variation'])->select('item.id','item.item_name','item.item_description','item.delivery_time','item.item_status','categories.category_name','item.addons_id','item.tax','item.ingredients_id')
    	->join('categories','item.cat_id','=','categories.id')
    	->where('item.id',$request->item_id)->first();

        $arr = explode(',', $itemdata->addons_id);
        foreach ($arr as $value) {
            $addons['value'] = Addons::whereIn('id',$arr)
            ->where('is_available','=','1')
            ->where('is_deleted','=','2')
            ->get();
        };

        $irr = explode(',', $itemdata->ingredients_id);
        foreach ($irr as $value) {
            $getingredients['value'] = Ingredients::select(\DB::raw("CONCAT('".url('/storage/app/public/images/ingredients/')."/', image) AS ingredients_image"))->whereIn('id',$irr)->get();
        };

        $data = array(
            'id' => $itemdata->id,
            'item_name' => $itemdata->item_name,
            'item_description' => $itemdata->item_description,
            'delivery_time' => $itemdata->delivery_time,
            'item_status' => $itemdata->item_status,
            'category_name' => $itemdata->category_name,
            'tax' => $itemdata->tax,
            'images' => $itemdata->itemimagedetails,
            'variation' => $itemdata->variation,
            'ingredients' => $getingredients['value'],
            'addons' => $addons['value'],
            
        ); 

        if(!empty($data))
        {
        	return response()->json(['status'=>1,'message'=>'Item Successful','data'=>$data],200);
        }
        else
        {
            return response()->json(['status'=>0,'message'=>trans('messages.no_data')],200);
        }
    }

    public function relateditem(Request $request)
    {
        if($request->item_id == ""){
            return response()->json(["status"=>0,"message"=>"category is required"],400);
        }

        $getcategory = Item::where('id','=',$request->item_id)->first();

        if($request->user_id == ""){
            $user_id  = $request->user_id;
            
            $relatedproduct = Item::with(['category','itemimage','variation'])->select('item.id','item.item_name',DB::raw('(case when favorite.item_id is null then 0 else 1 end) as is_favorite'))
            ->leftJoin('favorite', function($query) use($user_id) {
                $query->on('favorite.item_id','=','item.id')
                ->where('favorite.user_id', '=', $user_id);
            })->where('cat_id','=',$getcategory->cat_id)->where('item.id','!=',$request->item_id)->orderBy('id', 'DESC')->paginate(10);;

            if(!empty($relatedproduct))
            {
                return response()->json(['status'=>1,'message'=>'Item Successful','data'=>$relatedproduct],200);
            }
            else
            {
                return response()->json(['status'=>0,'message'=>'No data found'],200);
            }
            return response()->json(["status"=>0,"message"=>"category is required"],400);
        } else {
            $checkuser=User::where('id',$request->user_id)->first();

            if($checkuser->is_available == '1') 
            {
                $user_id  = $request->user_id;
                $relatedproduct = Item::with(['category','itemimage','variation'])->select('item.id','item.item_name',DB::raw('(case when favorite.item_id is null then 0 else 1 end) as is_favorite'))
                ->leftJoin('favorite', function($query) use($user_id) {
                    $query->on('favorite.item_id','=','item.id')
                    ->where('favorite.user_id', '=', $user_id);
                })->where('cat_id','=',$getcategory->cat_id)->where('item.id','!=',$request->item_id)->orderBy('id', 'DESC')->paginate(10);;

                if(!empty($relatedproduct))
                {
                    return response()->json(['status'=>1,'message'=>'Item Successful','data'=>$relatedproduct],200);
                }
                else
                {
                    return response()->json(['status'=>0,'message'=>'No data found'],200);
                }
                return response()->json(["status"=>0,"message"=>"category is required"],400);
            } else {
                $status=2;
                $message='Your account has been blocked by Admin';
                return response()->json(['status'=>$status,'message'=>$message],422);
            }
        }
    }

    public function searchitem()
    {
        $itemdata=Item::select('id','item_name')
        ->where('item.item_status','1')
        ->where('item.is_deleted','2')
        ->orderBy('item.id', 'DESC')
        ->get();
        
        if(!$itemdata->isEmpty())
        {
            return response()->json(['status'=>1,'message'=>'Item Successful','data'=>$itemdata],200);
        }
        else
        {
            return response()->json(['status'=>0,'message'=>trans('messages.no_data')],200);
        }
    }

    public function addfavorite(Request $request)
    {
        if($request->user_id == ""){
            return response()->json(["status"=>0,"message"=>trans('messages.user_id_required')],400);
        }
        if($request->item_id == ""){
            return response()->json(["status"=>0,"message"=>trans('messages.item_required')],400);
        }

        $data=Favorite::where([
            ['favorite.user_id',$request['user_id']],
            ['favorite.item_id',$request['item_id']]
        ])
        ->get()
        ->first();
        try {

            if($data=="") {
                $favorite = new Favorite;
                $favorite->user_id =$request->user_id;
                $favorite->item_id =$request->item_id;
                $favorite->save();

                return response()->json(['status'=>1,'message'=>trans('messages.favorite_list')],200);
            } else {
                return response()->json(['status'=>0,'message'=>trans('messages.favorite_available')],400);
            }
            
        } catch (\Exception $e){
            return response()->json(['status'=>0,'message'=>trans('messages.wrong')],400);
        }
    }

    public function favoritelist(Request $request)
    {
        if($request->user_id == ""){
            return response()->json(["status"=>0,"message"=>trans('messages.user_id_required')],400);
        }

        $favorite=Favorite::with(['itemimage','variation'])->select('favorite.id as favorite_id','item.id','item.item_name')
        ->join('item','favorite.item_id','=','item.id')
        ->where('item.item_status','1')
        ->where('item.is_deleted','2')
        ->where('favorite.user_id',$request['user_id'])->paginate(10); 

        if(!empty($favorite))
        {
            return response()->json(['status'=>1,'message'=>'Favorite List','data'=>$favorite],200);
        }
        else
        {
            return response()->json(['status'=>0,'message'=>trans('messages.no_data')],200);
        }
    }

    public function removefavorite(Request $request)
    {
        if($request->favorite_id == ""){
            return response()->json(["status"=>0,"message"=>trans('messages.item_required')],400);
        }

        $favorite=Favorite::where('id', $request->favorite_id)->delete();

        if($favorite)
        {
            return response()->json(['status'=>1,'message'=>trans('messages.delete')],200);
        }
        else
        {
            return response()->json(['status'=>0,'message'=>trans('messages.no_data')],200);
        }
    }
}