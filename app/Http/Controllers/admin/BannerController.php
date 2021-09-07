<?php

namespace App\Http\Controllers\admin;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Banner;
use App\Item;
use App\Category;
use Validator;
class BannerController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
      $getbanner = Banner::all();
      $getitem = Item::where('item_status','1')->where('is_deleted','2')->get();
      $getcategory = Category::where('is_available','1')->where('is_deleted','2')->get();
      return view('banner',compact('getbanner','getitem','getcategory'));
    }

    public function list()
    {
      $getbanner = Banner::with('item')->with('category')->get();
      return view('theme.bannertable',compact('getbanner'));
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
          'image' => 'required|mimes:jpeg,png,jpg',
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
            $image = 'banner-' . uniqid() . '.' . $request->image->getClientOriginalExtension();
            $request->image->move('storage/app/public/images/banner', $image);

            $banner = new Banner;
            $banner->image =$image;
            $banner->item_id =$request->item_id;
            $banner->cat_id =$request->cat_id;
            $banner->type =$request->type;
            $banner->save();
            $success_output = 'Banner Added Successfully!';
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
        $banner = Banner::findorFail($request->id);
        $getbanner = Banner::where('id',$request->id)->first();
        if($getbanner->image){
            $getbanner->image=url('storage/app/public/images/banner/'.$getbanner->image);
        }
        return response()->json(['ResponseCode' => 1, 'ResponseText' => 'Banner fetch successfully', 'ResponseData' => $getbanner], 200);
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
        $validation = Validator::make($request->all(),[
          'image' => 'mimes:jpeg,png,jpg',
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
            $banner = new Banner;
            $banner->exists = true;
            $banner->id = $request->id;
            $banner->type = $request->type;
            if ($request->type == "category") {
                $banner->item_id = 0;
                $banner->cat_id = $request->cat_id;
            } else {
                $banner->cat_id = 0;
                $banner->item_id = $request->item_id;
            }

            if(isset($request->image)){
                if($request->hasFile('image')){
                    $image = $request->file('image');
                    $image = 'banner-' . uniqid() . '.' . $request->image->getClientOriginalExtension();
                    $request->image->move('storage/app/public/images/banner', $image);
                    $banner->image=$image;
                }            
            }
            $banner->save();           

            $success_output = 'Banner updated Successfully!';
        }
        $output = array(
            'error'     =>  $error_array,
            'success'   =>  $success_output
        );
        echo json_encode($output);
    }

    public function updateorder(Request $request)
    {
        $posts = Banner::all();

        foreach ($posts as $post) {
            
            foreach ($request->order as $order) {
                if ($order['id'] == $post->id) {
                    $post->update(['order' => $order['position']]);
                }
            }
        }
        
        if ($post) {
            return 1;
        } else {
            return 0;
        }
    }

    public function status(Request $request)
    {
        $banner = Banner::where('id', $request->id)->update( array('is_available'=>$request->status) );
        if ($banner) {
            return 1;
        } else {
            return 0;
        }
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy(Request $request)
    {
        $banner=Banner::where('id', $request->id)->delete();
        if ($banner) {
            return 1;
        } else {
            return 0;
        }
    }
}
