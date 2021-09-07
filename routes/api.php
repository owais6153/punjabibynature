<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| is assigned the "api" middleware group. Enjoy building your API!
|
*/

Route::middleware('auth:api')->get('/user', function (Request $request) {
    return $request->user();
});

Route::group(['namespace'=>'Api'],function (){

    if (\App\SystemAddons::where('unique_identifier', 'otp')->first() != null && \App\SystemAddons::where('unique_identifier', 'otp')->first()->activated) {
        Route::post('login','UserotpController@login');
        Route::post('register','UserotpController@register');
        Route::post('otpverify','UserotpController@otpverify');
        Route::post('editprofile','UserotpController@editprofile');
        Route::post('getprofile','UserotpController@getprofile');
        Route::post('changepassword','UserotpController@changepassword');
        Route::post('forgotPassword','UserotpController@forgotPassword');
        Route::get('restaurantslocation','UserotpController@restaurantslocation');
        Route::post('resendotp','UserotpController@resendotp');
        Route::post('contact','UserotpController@contact');
        Route::get('checkaddons','UserotpController@checkaddons');

        Route::post('checkpincode','CheckoutotpController@checkpincode');
        Route::post('summary','CheckoutotpController@summary');
        Route::post('order','CheckoutotpController@order');
        Route::post('orderhistory','CheckoutotpController@orderhistory');
        Route::post('getorderdetails','CheckoutotpController@getorderdetails');
        Route::post('ordercancel','CheckoutotpController@ordercancel');
        Route::get('promocodelist','CheckoutotpController@promocodelist');
        Route::post('promocode','CheckoutotpController@promocode');
        Route::post('addmoney','CheckoutotpController@addmoney');
        Route::post('wallet','CheckoutotpController@wallet');
        Route::post('paymenttype','CheckoutotpController@paymenttype');

        //Vendor
        Route::post('adminlogin','AdminotpController@login');
        Route::post('home','AdminotpController@home');
        Route::post('history','AdminotpController@history');
        Route::post('orderdetails','AdminotpController@orderdetails');
        Route::post('update','AdminotpController@update');
        Route::get('drivers','AdminotpController@drivers');
        Route::post('assign','AdminotpController@assign');
        Route::post('updatepassword','AdminotpController@updatepassword');
        Route::post('updateprofile','AdminotpController@updateprofile');
    } else {
        Route::post('login','UserController@login');
        Route::post('register','UserController@register');
        Route::post('otpverify','UserController@otpverify');
        Route::post('editprofile','UserController@editprofile');
        Route::post('getprofile','UserController@getprofile');
        Route::post('changepassword','UserController@changepassword');
        Route::post('forgotPassword','UserController@forgotPassword');
        Route::get('restaurantslocation','UserController@restaurantslocation');
        Route::post('resendotp','UserController@resendotp');
        Route::post('contact','UserController@contact');
        Route::get('checkaddons','UserController@checkaddons');

        Route::post('checkpincode','CheckoutController@checkpincode');
        Route::post('summary','CheckoutController@summary');
        Route::post('order','CheckoutController@order');
        Route::post('orderhistory','CheckoutController@orderhistory');
        Route::post('getorderdetails','CheckoutController@getorderdetails');
        Route::post('ordercancel','CheckoutController@ordercancel');
        Route::get('promocodelist','CheckoutController@promocodelist');
        Route::post('promocode','CheckoutController@promocode');
        Route::post('addmoney','CheckoutController@addmoney');
        Route::post('wallet','CheckoutController@wallet');
        Route::post('paymenttype','CheckoutController@paymenttype');

        //Vendor
        Route::post('adminlogin','AdminController@login');
        Route::post('home','AdminController@home');
        Route::post('history','AdminController@history');
        Route::post('orderdetails','AdminController@orderdetails');
        Route::post('update','AdminController@update');
        Route::get('drivers','AdminController@drivers');
        Route::post('assign','AdminController@assign');
        Route::post('updatepassword','AdminController@updatepassword');
        Route::post('updateprofile','AdminController@updateprofile');
    }

    Route::get('isopenclose','TimeController@isopenclose');

    Route::get('category','CategoryController@category');
    Route::post('item','ItemController@item');
    Route::post('itemdetails','ItemController@itemdetails');
    Route::get('searchitem','ItemController@searchitem');
    Route::post('addfavorite','ItemController@addfavorite');
    Route::post('favoritelist','ItemController@favoritelist');
    Route::post('removefavorite','ItemController@removefavorite');
    Route::post('latestitem','ItemController@latestitem');
    Route::post('relateditem','ItemController@relateditem');

    Route::post('cart','CartController@cart');
    Route::post('cartcount','CartController@cartcount');
    Route::post('getcart','CartController@getcart');
    Route::post('qtyupdate','CartController@qtyupdate');
    Route::post('deletecartitem','CartController@deletecartitem');

    Route::get('banner','BannerController@banner');
    Route::get('pincode','PincodeController@pincode');

    Route::post('ratting','RattingController@ratting');
    Route::get('rattinglist','RattingController@rattinglist');

    Route::post('address','AddressController@address');
    Route::post('getaddress','AddressController@getaddress');
    Route::post('updateaddress','AddressController@updateaddress');
    Route::post('deleteaddress','AddressController@deleteaddress');

    //Driver
    Route::post('driverlogin','DriverController@login');
    Route::post('drivergetprofile','DriverController@getprofile');
    Route::post('drivereditprofile','DriverController@editprofile');
    Route::post('driverchangepassword','DriverController@changepassword');
    Route::post('driverforgotPassword','DriverController@forgotPassword');
    Route::post('driverongoingorder','DriverController@ongoingorder');
    Route::post('driverorder','DriverController@orderhistory');
    Route::post('driverorderdetails','DriverController@getorderdetails');
    Route::post('delivered','DriverController@delivered');
});