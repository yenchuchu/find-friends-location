<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Input;
use Illuminate\Support\Facades\Redirect;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\Facades\Validator;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Hash;
use Illuminate\Http\Request;

use Illuminate\Foundation\Auth\User as Authenticatable;

class Register extends Authenticatable
{
    protected $table = "register_users";
    public static function formStore($data) {
        $username = Input::get('name');
        $email = Input::get('email');
        $password = Hash::make(Input::get('password'));

        $user = new Register();

        $user->name = $username;
        $user->email = $email;
        $user->password = $password;
        $user->save();
    }
}
