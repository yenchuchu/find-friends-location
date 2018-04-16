<?php

namespace App\Backend;


use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Input;
use Illuminate\Support\Facades\Redirect;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\Facades\Validator;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Hash;
use Illuminate\Http\Request;

use Illuminate\Foundation\Auth\User as Authenticatable;

/**
 * User Quản lý các admin
 * Class User
 * @package App
 */
class UserSystem extends Authenticatable
{
    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $table = 'user_systems';
    protected $fillable = [
        'display_name', 'email', 'password', 'address', 'phone', 'total_info', 'status', 'last_login', 'ip_address',
        'user_token', 'avatar',
    ];

    /**
     * The attributes that should be hidden for arrays.
     *
     * @var array
     */
    protected $hidden = [
        'password', 'remember_token',
    ];

    public static function formStore($data) {
        $username = Input::get('name');
        $email = Input::get('email');
        $password = Hash::make(Input::get('password'));

        $user = new UserSystem();

        $user->display_name = $username;
        $user->email = $email;
        $user->password = $password;
        $user->save();
    }
}
