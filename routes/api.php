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

    #login - register - forget password user
    Route::post('/register', 'RegisterController@register')->name('frontend.user.register');
    Route::post('/login', 'LoginController@login')->name('frontend.user.login');
    Route::post('/forget-password', 'LoginController@forgetPassword')->name('frontend.user.forgot.password');

    # user send email to required reset password
    Route::post('/require-reset-password', array('uses' => 'LoginController@requiredResetPassword', 'as' => 'api.send.required.reset.password'));
    #reset pw for user
    Route::post('reset-password', array('uses' => 'LoginController@resetPassword', 'as' => 'api.reset.password'));

    # list friends own auth
    Route::get('/list-friends', 'UserController@getListFriends')->name('api.list.friends');
    # search uesrs
    Route::get('/search-users', 'UserController@searchUsers')->name('api.search.users');
    # get profile own auth
    Route::get('/profile', 'UserController@profile')->name('api.get.profile');

    # manage location users
    Route::group(['prefix' => 'location'], function () {
        Route::post('/send', 'ShareUserController@createSharing')->name('api.create.sharing');
        Route::post('/change-status', 'ShareUserController@changeStatusSharing')->name('api.change.sharing');
        Route::post('/delete', 'ShareUserController@deleteSharing')->name('api.delete.sharing');
    });


});