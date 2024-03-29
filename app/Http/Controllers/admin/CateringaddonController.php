<?php
namespace App\Http\Controllers\admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;
use App\Cateringaddon;
use App\Cateringtypes;
use Validator;
use App\Category;
use App\Cart;
use App\Item;


//        $getCateringaddon = Cateringaddon::all();
//        $getCateringtypes = Cateringtypes::all();

class CateringaddonController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $addonGroups = Cateringtypes::all();
        $getaddons = Cateringaddon::where('is_deleted','2')->where('is_available', '1')->get();
        return view('Cateringaddon',compact('getaddons', 'addonGroups'));
    }

    public function list()
    {
        $getaddons = Cateringaddon::where('is_deleted','2')->get();

        return view('theme.addonstable',compact('getaddons'));
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
        $validation = Validator::make($request->all(),[
          'name' => 'required',
          'type' => 'required',
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
            if ($request->type == "free") {
                $price = "0";
            } else {
                $price = $request->price;
            }
            $addons = new Cateringaddon;
            $addons->name =$request->name;
            $addons->price =$price;
            
            $addons->group_id = $request->group_id;
            
            $addons->save();
            $success_output = 'Addons Added Successfully!';
        }
        $output = array(
            'error'     =>  $error_array,
            'success'   =>  $success_output
        );
        echo json_encode($output);
    }

    // /**
    //  * Display the specified resource.
    //  *
    //  * @param  int  $id
    //  * @return \Illuminate\Http\Response
    //  */
    public function show(Request $request)
    {
         $addons = Cateringaddon::findorFail($request->id);
        return response()->json(['ResponseCode' => 1, 'ResponseText' => 'addons fetch successfully', 'ResponseData' => $addons], 200);
    }

    // /**
    //  * Show the form for editing the specified resource.
    //  *
    //  * @param  int  $id
    //  * @return \Illuminate\Http\Response
    //  */
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

        $validation = Validator::make($request->all(),[
            'name' => 'required',
            'type' => 'required',
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
            $UpdateCart = Cart::where('addons_id', 'LIKE', '%' . $request->id . '%')
                            ->delete();
            $addons = new Cateringaddon;
            $addons->exists = true;
            $addons->id = $request->id;

            if ($request->type == "free") {
                $price = "0";
            } else {
                $price = $request->price;
            }
            $addons->name =$request->name;
            $addons->price =$price;
            $addons->group_id = $request->group_id;            
            $addons->save();

            $cartdelete=Cart::where('addons_id', $request->id)->delete();

            $success_output = 'Addons updated Successfully!';
        }
        $output = array(
            'error'     =>  $error_array,
            'success'   =>  $success_output
        );
        echo json_encode($output);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy(Request $request)
    {
        $addons=Cateringaddon::where('id', $request->id)->delete();
        if ($addons) {
            return 1;
        } else {
            return 0;
        }
    }

    public function status(Request $request)
    {
        
        $category = Cateringaddon::where('id', $request->id)->update( array('is_available'=>$request->status) );

        $UpdateCart = Cart::where('addons_id', 'LIKE', '%' . $request->id . '%')->update( array('is_available'=>$request->status) );
        if ($category) {
            return 1;
        } else {
            return 0;
        }
    }

    public function delete(Request $request)
    {
        $UpdateDetails = Cateringaddon::where('id', $request->id)
                    ->update(['is_deleted' => '1']);
        $UpdateCart = Cart::where('addons_id', 'LIKE', '%' . $request->id . '%')
                            ->delete();
        if ($UpdateDetails) {
            return 1;
        } else {
            return 0;
        }
    }
}
