<?php

/*
|--------------------------------------------------------------------------
| OTP Routes
|--------------------------------------------------------------------------
|
| Here is where you can register admin routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| contains the "web" middleware group. Now create something great!
|
*/


//Admin
Route::group(['prefix' => 'admin', 'namespace' => 'admin'], function () {
    Route::group(['middleware' => ['AdminAuth']],function(){
        Route::get('otp-configuration', 'OTPConfigurationController@index');
        Route::post('otp-configuration/update', 'OTPConfigurationController@update');
    });
});
