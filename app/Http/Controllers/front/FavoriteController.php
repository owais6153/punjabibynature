<?php

namespace App\Http\Controllers\front;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Cart;
use App\Item;
use App\ItemImages;
use App\About;
use App\Favorite;
use App\User;
use Session;

class FavoriteController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index() {
        $getabout = About::where('id','=','1')->first();
        $favorite=Favorite::with('itemimage')->with('variation')->select('favorite.id as favorite_id','item.id','item.item_name','item.item_description')
        ->join('item','favorite.item_id','=','item.id')
        ->where('item.item_status','1')
        ->where('item.is_deleted','2')
        ->where('favorite.user_id',Session::get('id'))->paginate(9);

        $getdata=User::select('currency')->where('type','1')->first();

        return view('front.favorite',compact('favorite','getabout','getdata'));
    }
}
