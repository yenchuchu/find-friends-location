<?php

namespace App\Http\Controllers;

use App\Register;
use Illuminate\Support\Facades\Input;
use Illuminate\Support\Facades\Redirect;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\Facades\Validator;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Hash;
use Illuminate\Http\Request;
use Auth;

class RegisterController extends Controller
{
    public function __construct()
    {
        $this->middleware('guest');
    }

    public function showRegistrationForm()
    {
        return view('auth.register');
    }

    public function register(Request $request) {
        $allRequest = $request->all();
        $data = [
            'name' => $allRequest['name'],
            'email' => $allRequest['email'],
            'password_confirmation' => $allRequest['password_confirmation'],
            'password' => $allRequest['password'],
        ];

        $validate= Validator::make(
            $data,
            [
                'name' =>'required|min:5|max:255',
                'email' => 'email|required|unique:register_users|max:255',
                'password' => 'required|min:6',
                'password_confirmation' => 'required|same:password',
            ],

            [
                'required'=>':attribute không được để trống',
                'email'=>':attribute không đúng định dạng',
                'min'=>':attribute không được nhỏ hơn :min',
                'unique'=>':attribute đã tồn tại trong hệ thống',
                'max'=>':attribute không được lớn hơn :max',
                'password_confirmation.same'=>'Mật khẩu không khớp',
            ],
            [
                'name' =>'Name',
                'email' => 'Email',
                'password' => 'Password',
                'password_confirmation' => 'Password Confirm',
            ]

        );
        if($validate->fails()){
            return Redirect::to('register')->withErrors($validate);
        } else {
            Register::formStore(Input::except(array('_token', 'password_confirmation')));

            return Redirect::to('register')->with('success', 'Success');
        }
    }

    public function showLoginForm() {

        return view('auth.login');
    }

    /**
     * Login by email and password
     */
    public function login(Request $request) {
        $data = Input::except(array('_token'));

        $validate= Validator::make(
            $data,
            [
                'email' => 'email|required|max:255',
                'password' => 'required|min:6',
            ],

            [
                'required'=>':attribute Không được để trống',
                'email'=>':attribute Không đúng định dạng',
                'min'=>':attribute Không được nhỏ hơn :min',
                'unique'=>':attribute đã tồn tại trong hệ thống',
                'max'=>':attribute Không được lớn hơn :max',
            ],
            [
                'email' => 'Email',
                'password' => 'Password',
            ]
        );

        if($validate->fails()){
            return Redirect::to('login')->withErrors($validate);
        } else {
             $data = Input::except((array('_token')));

             if(Auth::attempt($data)) {
//                 echo 'yes';
                 return Redirect::to('home');
             } else {
//                 echo 'no';
                 return Redirect::to('login');
             }
        }
    }

}
