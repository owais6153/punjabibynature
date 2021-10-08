<?php

namespace App\Http\Controllers\front;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\DB;
use App\Category;
use App\Item;
use App\ItemImages;
use App\Ingredients;
use App\IngredientTypes;
use App\Favorite;
use App\Cart;
use App\About;
use App\User;
use App\Addons;
use App\AddonGroups;
use App\ComboItem;
use App\ComboGroup;
use App\Address;
use App\Payment;
use Session;
use URL;


class ItemController extends Controller
{
    /**3
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index() {
        $getcategory = Category::where('is_available','=','1')->where('is_deleted','2')->get();
        $getabout = About::where('id','=','1')->first();
        $user_id  = Session::get('id');
        $getitem = Item::with(['category','itemimage','variation'])->select('item.cat_id','item.id','item.item_name','item.item_description','item.item_status', 'item.item_type' ,DB::raw('(case when favorite.item_id is null then 0 else 1 end) as is_favorite'))
        ->where('item.item_type', '=', 'product')
        ->leftJoin('favorite', function($query) use($user_id) {
            $query->on('favorite.item_id','=','item.id')
            ->where('favorite.user_id', '=', $user_id);
        $cartdata=Cart::with('itemimage')->select('id','qty','price','item_notes','cart.variation','item_name','tax',\DB::raw("CONCAT('".url('/storage/app/public/images/item/')."/', item_image) AS item_image"),'item_id','addons_id','addons_name','addons_price')
        ->where('user_id',$user_id)
        ->where('is_available','=','1')->get();
        })
        ->where('item.item_status','1')->where('item.is_deleted','2')
        ->orderBy('id', 'DESC')->paginate(9);

        $getdata=User::select('currency')->where('type','1')->first();

        if(empty($getitem)){ 
            abort(404); 
        } else {
              if (Session::get('id')) {
            $cartdata=Cart::with('itemimage')->select('id','qty','price','item_notes','cart.variation','item_name','tax',\DB::raw("CONCAT('".url('/storage/app/public/images/item/')."/', item_image) AS item_image"),'item_id','addons_id','addons_name','addons_price')
            ->where('user_id',$user_id)
            ->where('is_available','=','1')->get();
        }
        else{
            $cartdata_temp = Session::get('guest_cart');
            $cartdata = json_decode(json_encode($cartdata_temp));
       
        //     // exit();
        }
            return view('front.product',compact('getcategory','getabout','getitem','getdata','cartdata'));   
        }
    }

    public function productdetails(Request $request) {
        $guest_cart = Session::get('guest_cart');

        $getcategory = Category::where('is_available','=','1')->where('is_deleted','2')->get();
        $user_id  = Session::get('id');
        $getabout = About::where('id','=','1')->first();

        $getitem = Item::with('category')->select('item.*',DB::raw('(case when favorite.item_id is null then 0 else 1 end) as is_favorite'))
        ->leftJoin('favorite', function($query) use($user_id) {
            $query->on('favorite.item_id','=','item.id')
            ->where('favorite.user_id', '=', $user_id);
        })->where('item.id','=',$request->id)->where('item.item_status','1')->where('item.is_deleted','2')->first();

        if(empty($getitem)){ 
            abort(404); 
        } else {

            $arr = explode(',', $getitem->addons_id);
            foreach ($arr as $value) {
                $freeaddons['value'] = Addons::whereIn('id',$arr)
                ->where('is_available','=','1')
                ->where('is_deleted','=','2')
                ->where('price','=','0')
                ->get();
            };
            foreach ($arr as $value) {
                $paidaddons['value'] = Addons::whereIn('id',$arr)
                ->where('is_available','=','1')
                ->where('is_deleted','=','2')
                ->where('price','!=',"0")
                ->get();
            };

            $irr = explode(',', $getitem->ingredients_id);
            $irrAvailable = explode(',', $getitem->available_ing_option);
            $getingredientsByTypes = array();
            foreach ($irr as $key => $value) {
                $available_ing_option = ($irrAvailable[$key] == 'allow_all') ? "'all'" : intval($irrAvailable[$key]);
                $getingredientsByTypes[] =  IngredientTypes::with(['ingredients'])->select('ingredient_types.*', \DB::raw($available_ing_option . ' AS available_ing_option'))->where('ingredient_types.id', $value)->first();
               
                // print_r($getingredientsByTypes[0]->ingredients[1]->ingredients);

            }

            $getimages = ItemImages::select(\DB::raw("CONCAT('".url('/storage/app/public/images/item/')."/', image) AS image"))->where('item_id','=',$request->id)->get();

            $getcategory = Item::where('id','=',$request->id)->first();

            $user_id  = Session::get('id');
            $relatedproduct = Item::with(['category','itemimage','variation'])->select('item.cat_id','item.id','item.item_name','item.item_description',DB::raw('(case when favorite.item_id is null then 0 else 1 end) as is_favorite'))
            ->leftJoin('favorite', function($query) use($user_id) {
                $query->on('favorite.item_id','=','item.id')
                ->where('favorite.user_id', '=', $user_id);
            })
            ->where('item.item_status','1')->where('item.is_deleted','2')
            ->where('cat_id','=',$getcategory->cat_id)->where('item.id','!=',$request->id)->orderBy('id', 'DESC')->get();


            $addon_groups_id = explode(',', $getitem->addongroups_id);
            $available_addons_option = explode(',', $getitem->available_addons_option);
            foreach ($addon_groups_id as $key => $value) {
                $available_add = ($available_addons_option[$key] == 'allow_all') ? "'all'" : intval($available_addons_option[$key]);
                $getAddonsByGroups[] =  AddonGroups::with(['addons'])->select('addon_groups.*', \DB::raw($available_add . ' AS available_add_option'))->where('addon_groups.id', $value)->first();
               
                // print_r($getingredientsByTypes[0]->ingredients[1]->ingredients);

            }
        }
        $totalComboPrice = 0;
        $ComboGroupIDs = explode(',', $getitem->combo_group_id);
        foreach ($ComboGroupIDs as $ComboGroupIDindex => $ComboGroupID) {
            $ComboGroups[] = ComboGroup::with('ComboItem')->where('combo_group.id', $ComboGroupID)->first();
            $totalComboPrice += ( isset($ComboGroups[$ComboGroupIDindex]->pric) ) ? $ComboGroups[$ComboGroupIDindex]->price : 0;
        }
        

        
        $getdata=User::select('currency')->where('type','1')->first();
        $getcategory = Category::where('is_available','=','1')->where('is_deleted','2')->get();
        


        if (Session::get('id')) {
            $cartdata=Cart::with('itemimage')->select('id','qty','price','item_notes','cart.variation','item_name','tax',\DB::raw("CONCAT('".url('/storage/app/public/images/item/')."/', item_image) AS item_image"),'item_id','addons_id','addons_name','addons_price')
            ->where('user_id',$user_id)
            ->where('is_available','=','1')->get();
        }
        else{
            $cartdata_temp = Session::get('guest_cart');
            $cartdata = json_decode(json_encode($cartdata_temp));       
        }


        return view('front.product-details', compact('getitem','getabout','getimages','freeaddons','paidaddons','relatedproduct','getdata', 'getingredientsByTypes', 'getAddonsByGroups', 'getcategory', 'ComboGroups', 'totalComboPrice', 'cartdata'));
    }

    public function show(Request $request)
    {
        
        $getcategory = Category::where('is_available','=','1')->where('is_deleted','2')->get();
        $getabout = About::where('id','=','1')->first();
        $user_id  = Session::get('id');
        $getitem = Item::with(['category','itemimage','variation'])->select('item.cat_id','item.id','item.item_name','item.item_description', 'item.item_type', 'item.item_status',DB::raw('(case when favorite.item_id is null then 0 else 1 end) as is_favorite'))
        ->where('item.item_type', '=', 'product')
        ->leftJoin('favorite', function($query) use($user_id) {
            $query->on('favorite.item_id','=','item.id')
            ->where('favorite.user_id', '=', $user_id);
        })
        ->where('item.item_status','1')->where('item.is_deleted','2')
        ->where('cat_id','=',$request->id)->orderBy('id', 'DESC')->paginate(9);

        $getdata=User::select('currency')->where('type','1')->first();
        if (Session::get('id')) {
            $cartdata=Cart::with('itemimage')->select('id','qty','price','item_notes','cart.variation','item_name','tax',\DB::raw("CONCAT('".url('/storage/app/public/images/item/')."/', item_image) AS item_image"),'item_id','addons_id','addons_name','addons_price')
            ->where('user_id',$user_id)
            ->where('is_available','=','1')->get();
        }
        else{
            $cartdata_temp = Session::get('guest_cart');
            $cartdata = json_decode(json_encode($cartdata_temp));
        }
        return view('front.product', compact('getcategory','getitem','getabout','getdata','cartdata'));
    }

    public function favorite(Request $request)
    {
        if($request->user_id == ""){
            return response()->json(["status"=>0,"message"=>trans('messages.user_required')],400);
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
            if ($data=="") {
                $favorite = new Favorite;
                $favorite->user_id =$request->user_id;
                $favorite->item_id =$request->item_id;
                $favorite->save();
                return 1;
            } else {
                return 0;
            }            
        } catch (\Exception $e){
            return response()->json(['status'=>0,'message'=>trans('messages.wrong')],200);
        }
    }

    public function unfavorite(Request $request)
    {
        if($request->user_id == ""){
            return response()->json(["status"=>0,"message"=>trans('messages.user_required')],400);
        }
        if($request->item_id == ""){
            return response()->json(["status"=>0,"message"=>trans('messages.item_required')],400);
        }

        $unfavorite=Favorite::where('user_id', $request->user_id)->where('item_id', $request->item_id)->delete();
        if ($unfavorite) {
            return 1;
        } else {
            return 0;
        }
    }

    public function addtocart(Request $request)
    {
        if($request->item_id == ""){
            return response()->json(["status"=>0,"message"=>"Item is required"],400);
        }
        if($request->qty == ""){
            return response()->json(["status"=>0,"message"=>"Qty is required"],400);
        }
        if($request->price == ""){
            return response()->json(["status"=>0,"message"=>"Price is required"],400);
        }
        if($request->variation_id == ""){
            return response()->json(["status"=>0,"message"=>"Variation is required"],400);
        }
        if($request->user_id == ""){
            return response()->json(["status"=>0,"message"=>"User ID is required"],400);
        }
        $getitem=Item::with('itemimage')->select('item.id','item.item_name','item.tax')
                ->where('item.id',$request->item_id)->first();
        
        if ($request->user_id == "guest") {
            
                      

            $cartDetails = array(
                array(
                    'item_id' => $request->item_id,
                    'addons_id' => $request->addons_id,
                    'qty' => $request->qty,
                    'price' => $request->price,
                    'variation_id' => $request->variation_id,
                    'variation_price' => $request->variation_price,
                    'variation' => $request->variation,
                    'user_id' => 'guest',
                    'item_notes' => $request->item_notes,
                    'item_name' => $getitem->item_name,
                    'tax' => $getitem->tax,
                    'item_image' =>  $getitem['itemimage']->image_name,
                    'addons_name' => $request->addons_name,
                    'addons_price' => $request->addons_price,   
                    'ingredients' => $request->ingredients, 
                    'combo' => $request->combo,
                    'group_addons' => $request->group_addons,
                    'totalAddonPrice' => $request->totalAddonPrice,
                )                               
            );
            $guestCartData = array();
            $data = collect($cartDetails);
            if (!Session::has('guest_cart')) {
                Session::put('guest_cart', $data);
                return response()->json(['status'=>1,'message'=>'Item has been added to your cart','cartcnt'=>1]);
            }
       
            else{
                $guestCartData = (Session::has('guest_cart')) ? Session::get('guest_cart') : array() ;
                $cartDetails = 
                array(
                    'item_id' => $request->item_id,
                    'addons_id' => $request->addons_id,
                    'qty' => $request->qty,
                    'price' => $request->price,
                    'variation_id' => $request->variation_id,
                    'variation_price' => $request->variation_price,
                    'variation' => $request->variation,
                    'user_id' => 'guest',
                    'item_notes' => $request->item_notes,
                    'item_name' => $getitem->item_name,
                    'tax' => $getitem->tax,
                    'item_image' => $getitem['itemimage']->image_name,
                    'addons_name' => $request->addons_name,
                    'addons_price' => $request->addons_price,   
                    'ingredients' => $request->ingredients,   
                    'combo' => $request->combo,
                    'group_addons' => $request->group_addons,
                    'totalAddonPrice' => $request->totalAddonPrice,
                 );
                

                $guestCartData[] = $cartDetails;                    
                $data = collect($guestCartData);
                Session::put('guest_cart', $data);
                $count = count($guestCartData);
                Session::put('cart', $count);
                //return response()->json(['ok' => 'okies2']);
                return response()->json(['status'=>1,'message'=>'Item has been added 1 to your cart','cartcnt'=>$count],200);
               
            }
        }
        



        
        else{
            try {

                

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

                $cart->ingredients =  (isset($request->ingredients) && !empty($request->ingredients)) ? implode('|',$request->ingredients) : null;
                $cart->combo = (isset($request->combo) && !empty($request->combo)) ? implode('|',$request->combo) : null;
                $cart->group_addons = (isset($request->group_addons) && !empty($request->group_addons)) ? implode('|',$request->group_addons) : null;
                $cart->totalAddonPrice =$request->totalAddonPrice;

                $cart->save();

                $count=Cart::where('user_id',$request->user_id)->count();

                Session::put('cart', $count);
                return response()->json(['status'=>1,'message'=>'Item has been 2 added to your cart','cartcnt'=>$count],200);

            } catch (\Exception $e){

                return response()->json(['status'=>0,'message'=>'Something went wrong', 'error' => $e],400);
            }
        } 
    }

    public function search(Request $request)
    {
        $getcategory = Category::where('is_available','=','1')->where('is_deleted','2')->get();
        $getabout = About::where('id','=','1')->first();
        $user_id  = Session::get('id');
        $getitem = Item::with(['category','itemimage','variation'])->select('item.cat_id','item.id','item.item_name','item.item_description',DB::raw('(case when favorite.item_id is null then 0 else 1 end) as is_favorite'))
        ->leftJoin('favorite', function($query) use($user_id) {
            $query->on('favorite.item_id','=','item.id')
            ->where('favorite.user_id', '=', $user_id);
        })->where('item.item_name','LIKE','%' . $request->item . '%')->where('item.item_status','1')->where('item.is_deleted','2')->orderBy('id', 'DESC')->paginate(9);

        $getdata=User::select('currency')->where('type','1')->first();
            if (Session::get('id')) {
            $cartdata=Cart::with('itemimage')->select('id','qty','price','item_notes','cart.variation','item_name','tax',\DB::raw("CONCAT('".url('/storage/app/public/images/item/')."/', item_image) AS item_image"),'item_id','addons_id','addons_name','addons_price')
            ->where('user_id',$user_id)
            ->where('is_available','=','1')->get();
        }
        else{
            $cartdata_temp = Session::get('guest_cart');
            $cartdata = json_decode(json_encode($cartdata_temp));
       
        //     // exit();
        }
        return view('front.search', compact('getcategory','getabout','getitem','getdata', 'cartdata'));
    }

    public function searchitem(Request $request)
    {
        if ($request->keyword != "") {
            $getitem = Item::select('id','item_name')
            ->where('item_name','LIKE','%' . $request->keyword . '%')->where('item_status','1')->where('is_deleted','2')->orderBy('id', 'DESC')->get();

            $output = '';
                         
            if (count($getitem)>0) {
                
                $output = '<ul class="list-group" style="display: block; position: relative; z-index: 1; height: 262px; overflow-y: scroll; overflow-x: hidden;">';
                
                foreach ($getitem as $row){
                    $output .= '<li class="list-group-item"><a href="'.URL::to('product-details/'.$row->id.'').'" style="font-weight: bolder;">'.$row->item_name.'</a></li>';
                }
                
                $output .= '</ul>';
            } else {
               
                $output .= '<li class="list-group-item" style="font-weight: bolder;">'.'No results'.'</li>';
            }
            return $output;
        }
        
    }
    public function getOptions(Request $request){
                $getcategory = Category::where('is_available','=','1')->where('is_deleted','2')->get();
        $user_id  = Session::get('id');
        $getabout = About::where('id','=','1')->first();

        $getitem = Item::with('category')->select('item.*',DB::raw('(case when favorite.item_id is null then 0 else 1 end) as is_favorite'))
        ->leftJoin('favorite', function($query) use($user_id) {
            $query->on('favorite.item_id','=','item.id')
            ->where('favorite.user_id', '=', $user_id);
        })->where('item.id','=',$request->id)->where('item.item_status','1')->where('item.is_deleted','2')->first();

        if(empty($getitem)){ 
            abort(404); 
        } else {

            $arr = explode(',', $getitem->addons_id);
            foreach ($arr as $value) {
                $freeaddons['value'] = Addons::whereIn('id',$arr)
                ->where('is_available','=','1')
                ->where('is_deleted','=','2')
                ->where('price','=','0')
                ->get();
            };
            foreach ($arr as $value) {
                $paidaddons['value'] = Addons::whereIn('id',$arr)
                ->where('is_available','=','1')
                ->where('is_deleted','=','2')
                ->where('price','!=',"0")
                ->get();
            };

            $irr = explode(',', $getitem->ingredients_id);
            $irrAvailable = explode(',', $getitem->available_ing_option);
            $getingredientsByTypes = array();
            foreach ($irr as $key => $value) {
                $available_ing_option = ($irrAvailable[$key] == 'allow_all') ? "'all'" : intval($irrAvailable[$key]);
                $getingredientsByTypes[] =  IngredientTypes::with(['ingredients'])->select('ingredient_types.*', \DB::raw($available_ing_option . ' AS available_ing_option'))->where('ingredient_types.id', $value)->first();
               
                // print_r($getingredientsByTypes[0]->ingredients[1]->ingredients);

            }

            $getimages = ItemImages::select(\DB::raw("CONCAT('".url('/storage/app/public/images/item/')."/', image) AS image"))->where('item_id','=',$request->id)->get();

            $getcategory = Item::where('id','=',$request->id)->first();

            $user_id  = Session::get('id');
            $relatedproduct = Item::with(['category','itemimage','variation'])->select('item.cat_id','item.id','item.item_name','item.item_description',DB::raw('(case when favorite.item_id is null then 0 else 1 end) as is_favorite'))
            ->leftJoin('favorite', function($query) use($user_id) {
                $query->on('favorite.item_id','=','item.id')
                ->where('favorite.user_id', '=', $user_id);
            })
            ->where('item.item_status','1')->where('item.is_deleted','2')
            ->where('cat_id','=',$getcategory->cat_id)->where('item.id','!=',$request->id)->orderBy('id', 'DESC')->get();


            $addon_groups_id = explode(',', $getitem->addongroups_id);
            $available_addons_option = explode(',', $getitem->available_addons_option);
            foreach ($addon_groups_id as $key => $value) {
                $available_add = ($available_addons_option[$key] == 'allow_all') ? "'all'" : intval($available_addons_option[$key]);
                $getAddonsByGroups[] =  AddonGroups::with(['addons'])->select('addon_groups.*', \DB::raw($available_add . ' AS available_add_option'))->where('addon_groups.id', $value)->first();
               
                // print_r($getingredientsByTypes[0]->ingredients[1]->ingredients);

            }
        }
        $totalComboPrice = 0;
        $ComboGroupIDs = explode(',', $getitem->combo_group_id);
        foreach ($ComboGroupIDs as $ComboGroupIDindex => $ComboGroupID) {
            $ComboGroups[] = ComboGroup::with('ComboItem')->where('combo_group.id', $ComboGroupID)->first();
            $totalComboPrice += ( isset($ComboGroups[$ComboGroupIDindex]->price) ) ? $ComboGroups[$ComboGroupIDindex]->price : 0;
        }
        

        
        $getdata=User::select('currency')->where('type','1')->first();
        $getcategory = Category::where('is_available','=','1')->where('is_deleted','2')->get();

        $source = $request->source;
        if($source == 'product'){

        $output = view('theme.addToCartModalBody', compact('getitem','getabout','getimages','freeaddons','paidaddons','relatedproduct','getdata', 'getingredientsByTypes', 'getAddonsByGroups', 'getcategory', 'ComboGroups', 'totalComboPrice', 'source'))->render();

        }
        else{
            $output = view('theme.addtocartcateringmodal', compact('getitem','getabout','freeaddons','paidaddons','relatedproduct','getdata', 'getingredientsByTypes', 'getAddonsByGroups', 'getcategory', 'source'))->render();

        }
        return response()->json(['status'=>1,'html'=> $output, 'title' => $getitem->item_name],200);
    }

      public function guestcheckout(){
        $user_id  = Session::get('id');
        $getdata=User::select('currency')->where('type','1')->first();
        $addressdata=Address::where('user_id',Session::get('id'))->orderBy('id', 'DESC')->get();
        $userinfo=User::select('name','email','mobile','wallet')->where('id',$user_id)
        ->get()->first();
        $getpaymentdata=Payment::select('payment_name','test_public_key','live_public_key','environment')->where('is_available','1')->orderBy('id', 'DESC')->get();
        $getabout = About::where('id','=','1')->first();
        $getcategory = Category::where('is_available','1')->where('is_deleted','2')->get();
        $taxval=User::select('currency','map')->where('type','1')->first();
        if (Session::get('id')) {
            $cartdata=Cart::with('itemimage')->select('id','qty','price','item_notes','cart.variation','item_name','tax',\DB::raw("CONCAT('".url('/storage/app/public/images/item/')."/', item_image) AS item_image"),'item_id','addons_id','addons_name','addons_price')
            ->where('user_id',$user_id)
            ->where('is_available','=','1')->get();
        }
        else{
            $cartdata_temp = Session::get('guest_cart');
            $cartdata = json_decode(json_encode($cartdata_temp));
       
        //     // exit();
        }
        return view('front.guestcheckout',compact('getabout','getdata','getcategory','cartdata','taxval','addressdata','userinfo','getpaymentdata'));
    }
}
