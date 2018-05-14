<?php

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| contains the "web" middleware group. Now create something great!
|
*/

Route::get('/home', 'HomeController@index');
Route::post('/logout', 'HomeController@logout')->name('user.logout');

//Route::get('/register', 'RegisterController@showRegistrationForm')->name('user.register.form');
//Route::post('/run-register', 'RegisterController@register')->name('user.register.store');


//Route::get('/login', 'RegisterController@showLoginForm')->name('user.login.form');
//Route::post('/run-login', 'RegisterController@login')->name('user.login.go');

Route::group(array('namespace' => 'Web'), function () {

    Route::get('/', 'PostController@index');

    Route::get('/register', 'RegisterController@showRegistrationForm')->name('user.register.form');
    Route::post('/run-register', 'RegisterController@register')->name('user.register.store');

    Route::get('/login', 'LoginController@showLoginForm')->name('user.login.form');
    Route::post('/run-login', 'LoginController@login')->name('user.login.go');

    Route::get('/dashboard', 'DashboardController@index')->name('dashboard');

    Route::get('/test-repository', 'PostController@index');
});

//Auth::routes();

Route::get('/home', 'HomeController@index');
