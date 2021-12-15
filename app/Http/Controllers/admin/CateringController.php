<?php
 
namespace App\Http\Controllers\admin;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Category;
use App\Item;
use App\ItemImages;
use App\Variation;
use App\Ingredients;
use App\IngredientTypes;
// use App\Addons;
// use App\AddonGroups;
use App\ComboItem;
use App\ComboGroup;
use App\Cateringtypes;
use App\Cateringaddon;
use App\Cart;
use Validator;
use App\CateringCat;

class CateringController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */

    public function index() {
        $getcategory = Category::where('is_available','1')->where('is_deleted','2')->get();
        $getingredients = Ingredients::where('is_deleted','2')->get();
        $getaddons = Cateringaddon::where('is_deleted','2')->where('is_available','1')->get();
        $getitem = Item::where('item_type','catering')->where('item.is_deleted','2')->get();
     

        return view('catering', compact('getcategory','getitem','getingredients','getaddons'));
    }

    public function addcatering() {
        $getcategory = Category::where('is_available','1')->where('is_deleted','2')->get();
        $getingredients = Ingredients::where('is_deleted','2')->get();
        $getaddons = Cateringaddon::where('is_deleted','2')->where('is_available','1')->get();        
        $getitem = Item::select('item.*')->with('category')->join('categories','item.cat_id','=','categories.id')->where('item.is_deleted','2')->where('categories.is_available','1')->get();
        $getingredientTypes = IngredientTypes::all();

        foreach ($getingredientTypes as $key => $value) {
           $value->countIngredients = $value->ingredients()->count();
        }

        $addonGroups = Cateringtypes::all();

        // dd($addonGroups);

        foreach ($addonGroups as $key => $value) {
           $value->countAddons = $value->cateringaddon()->count();
        }
        $getComboGroup = ComboGroup::all();
        foreach ($getComboGroup as $key => $value) {
           $value->countCombos = $value->ComboItem()->count();
        }        
        $getcateringcat = CateringCat::get();
        return view('addcatering', compact('getcategory','getitem','getingredients','getaddons', 'getingredientTypes', 'addonGroups', 'getComboGroup', 'getcateringcat'));
    }

    public function edititem($id) {
        $getcategory = Category::where('is_available','1')->where('is_deleted','2')->get();
        $getingredients = Ingredients::where('is_deleted','2')->get();
        $getaddons = Cateringaddon::where('is_deleted','2')->where('is_available','1')->get();
        $item = Item::findorFail($id);
        $getvariation = Variation::where('item_id', $id)->get();
        $getitem = Item::where('id',$id)->first();

        $getitemimages = ItemImages::where('item_id', $id)->get();
        $getingredientTypes = IngredientTypes::all();

        foreach ($getingredientTypes as $key => $value) {
           $value->countIngredients = $value->ingredients()->count();
        }
        $addonGroups = Cateringtypes::all();
        foreach ($addonGroups as $key => $value) {
           $value->countAddons = $value->cateringaddon()->count();
        }

        $getComboGroup = ComboGroup::all();
        foreach ($getComboGroup as $key => $value) {
           $value->countCombos = $value->ComboItem()->count();
        }       
        $getcateringcat = CateringCat::get(); 
        return view('edititem', compact('item','getitem','getcategory','getingredients','getaddons','getvariation','getitemimages', 'getingredientTypes', 'addonGroups', 'getComboGroup', 'getcateringcat'));
    }

    public function list()
    {
        $getitem = Item::select('item.*')->where('item.is_deleted','2')->get();
        return view('theme.itemtable',compact('getitem'));
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
        $this->validate($request,[
            // 'cat_id' => 'required',
            'food_type' => 'required',            
            'item_name' => 'required',
            'item_type' => 'required',
            'file.*' => 'required|mimes:jpeg,png,jpg',
            'product_price.*' => 'required|numeric',
            'sale_price.*' => 'required|numeric',
        ]);

        $item = new Item;
        
        $item->cat_id =$request->cat_id;
        $item->addons_id = (isset($request->addons_id)) ? @implode(",",$request->addons_id) : null;
        $item->ingredients_id = (isset($request->ingredients_id)) ? @implode(",",$request->ingredients_id) : null;
        $item->item_type =$request->item_type;
        $item->item_name =$request->item_name;
        $item->food_type =$request->food_type;
        $item->preparing_time =$request->preparation_time;
        $item->minimum_peeps =$request->minimum_people;
        $item->catering_cat_id =$request->catering_cat_id;
        $item->item_description =$request->description;
        $item->delivery_time =$request->delivery_time;
        $item->available_ing_option = (isset($request->available_ing_option)) ? @implode(",", $request->available_ing_option) : null;
        $item->addongroups_id = (isset($request->addons_groups_id)) ? @implode(",", $request->addons_groups_id) : null;
        $item->available_addons_option = (isset($request->available_addons_option)) ? @implode(",", $request->available_addons_option) : null;
        $item->tax =$request->tax;
        if (intval($request->make_combo) == '') {
            $request->make_combo = 0;
        }
        $item->is_default_combo = (isset($request->make_combo)) ? $request->make_combo : 0;
        $item->combo_group_id = (isset($request->combo_group)) ? @implode(",",$request->combo_group) : null;
        $item->save();

        $product_price = $request->product_price;
        $sale_price = $request->sale_price;
        $variation = $request->variation;

        $input['item_id'] =$item->id;
        $input['product_price'] = $product_price;
        $input['sale_price'] = '0';
        $input['variation'] = '0';

        Variation::create($input);


        // foreach($product_price as $key => $no)
        // {
        //     $input['item_id'] =$item->id;
        //     $input['product_price'] = $no;
        //     $input['sale_price'] = $sale_price[$key];
        //     $input['variation'] = $variation[$key];

        //     Variation::create($input);
        // }

        if ($request->hasFile('file')) {
            $files = $request->file('file');
            foreach($files as $file){

                $itemimage = new ItemImages;
                $image = 'item-' . uniqid() . '.' . $file->getClientOriginalExtension();

                

                $file->move('storage/app/public/images/item', $image);

                $itemimage->item_id =$item->id;
                $itemimage->image =$image;
                $itemimage->save();
            }
        }

        if ($item) {
            return redirect('admin/cateringproducts')->with('success', trans('messages.success'));
        } else {
            return redirect()->back()->with('danger', trans('messages.fail'));
        }
    }

    public function storeimages(Request $request)
    {
        $validation = Validator::make($request->all(),[
          'file.*' => 'required|mimes:jpeg,png,jpg'
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
            if ($request->hasFile('file')) {
                $files = $request->file('file');
                foreach($files as $file){

                    $itemimage = new ItemImages;
                    $image = 'item-' . uniqid() . '.' . $file->getClientOriginalExtension();

                    $file->move('storage/app/public/images/item', $image);

                    $itemimage->item_id =$request->itemid;
                    $itemimage->image =$image;
                    $itemimage->save();
                }
            }

            $success_output = 'Item Added Successfully!';
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
        
    }

    public function showimage(Request $request)
    {
        $getitem = ItemImages::where('id',$request->id)->first();
        if($getitem->image){
            $getitem->img=url('storage/app/public/images/item/'.$getitem->image);
        }
        return response()->json(['ResponseCode' => 1, 'ResponseText' => 'Item Image fetch successfully', 'ResponseData' => $getitem], 200);
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

        $this->validate($request,[
            'food_type' => 'required',  
            'item_name' => 'required',
            'item_type' => 'required',
            'product_price.*' => 'required|numeric',
            'sale_price.*' => 'required|numeric',
        ]);

        $validation = Validator::make($request->all(),[
          
        ]);

        $deletefromcart=Cart::where('item_id', $request->id)->delete();
        $item = new Item;
        $item->exists = true;
        $item->id = $request->id;

        $item->cat_id =$request->getcat_id;
        $item->addons_id =@implode(",",$request->addons_id);
        $item->ingredients_id =@implode(",",$request->ingredients_id);
        $item->available_ing_option = @implode(",", $request->available_ing_option);
        $item->item_name =$request->item_name;
        $item->item_type =$request->item_type;
        $item->food_type =$request->food_type;
        $item->catering_cat_id =$request->catering_cat_id;
        $item->item_description =$request->getdescription;
        $item->delivery_time =$request->getdelivery_time;
        $item->addongroups_id = @implode(",", $request->addons_groups_id);
        $item->available_addons_option = @implode(",", $request->available_addons_option);
        $item->tax =$request->tax;
        if (intval($request->make_combo) == '') {
            $request->make_combo = 0;
        }
        $item->is_default_combo =intval($request->make_combo);
        $item->combo_group_id = @implode(",",$request->combo_group);
        $item->save();   

        $product_price = $request->product_price;
        $sale_price = $request->sale_price;
        $variation = $request->variation;
        $variation_id = $request->variation_id;

                Variation::create($input);
        foreach($product_price as $key => $no)
        {
            if ($variation_id[$key] == "") {

                $input['item_id'] =$request->id;
                $input['product_price'] = $no;
                $input['sale_price'] = $sale_price[$key];
                $input['variation'] = $variation[$key];

            } 

            if ($variation_id[$key] != "") {
                
                $UpdateCart = Variation::where('id', $variation_id[$key])
                                    ->update(['product_price' => $no,'variation'=>$variation[$key],'sale_price'=>$sale_price[$key]]);
            }
        }

        if ($item) {
             return redirect('admin/item')->with('success', trans('messages.update'));
        } else {
            return redirect()->back()->with('danger', trans('messages.fail'));
        }
    }

    public function updateimage(Request $request)
    {
        $validation = Validator::make($request->all(),[
          'image' => 'image|mimes:jpeg,png,jpg'
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
            $itemimage = new ItemImages;
            $itemimage->exists = true;
            $itemimage->id = $request->id;

            if(isset($request->image)){
                if($request->hasFile('image')){
                    $image = $request->file('image');
                    $image = 'item-' . uniqid() . '.' . $request->image->getClientOriginalExtension();
                    $request->image->move('storage/app/public/images/item', $image);
                    $itemimage->image=$image;
                }            
            }
            $itemimage->save();
            $success_output = 'Item updated Successfully!';
        }
        $output = array(
            'error'     =>  $error_array,
            'success'   =>  $success_output
        );
        echo json_encode($output);
    }

    public function status(Request $request)
    {
        
        $UpdateDetails = Item::where('id', $request->id)
                    ->update(['item_status' => $request->status]);

        $deletefromcart=Cart::where('item_id', $request->id)->delete();
        if ($UpdateDetails) {
            return 1;
        } else {
            return 0;
        }
    }

    public function delete(Request $request)
    {
        $UpdateDetails = Item::where('id', $request->id)
                    ->update(['is_deleted' => '1']);
        $UpdateCart = Cart::where('item_id', @$request->id)
                            ->delete();
        if ($UpdateDetails) {
            return 1;
        } else {
            return 0;
        }
    }

    public function deletevariation(Request $request)
    {
        $getimg = Variation::where('item_id',$request->item_id)->count();

        if ($getimg > 1) {
            $UpdateDetails = Variation::where('id', $request->id)->delete();
            if ($UpdateDetails) {
                return 1;
            } else {
                return 0;
            }
        } else {
            return 2;
        }
        
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroyimage(Request $request)
    {
        $getitemimages = ItemImages::where('item_id', $request->item_id)->count();

        if ($getitemimages > 1) {
           $getimg = ItemImages::where('id',$request->id)->first();

           $itemimage=ItemImages::where('id', $request->id)->delete();
           if ($itemimage) {
               return 1;
           } else {
               return 0;
           }
        } else {
            return 2;
        }
    }
}
