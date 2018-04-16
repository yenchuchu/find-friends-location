<?php

use Illuminate\Http\Request;

header('Access-Control-Allow-Headers:Content-Type, Access-Control-Allow-Headers, Authorization, X-Requested-With,Access-Control-Allow-Origin');
header('Access-Control-Allow-Origin: *');
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

Route::group(array('namespace' => 'Frontend'), function () {

    Route::get('/register', 'RegisterController@register')->name('frontend.user.register');
    Route::get('/login', 'LoginController@login')->name('frontend.user.login');

//    Route::group(['middleware' => 'auth'], function () {
        Route::get('/users', 'UserController@index')->name('index');
//    });

});