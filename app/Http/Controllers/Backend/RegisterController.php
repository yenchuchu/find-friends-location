<?php

namespace App\Http\Controllers\Backend;

use App\Backend\UserSystem;
use App\Repositories\Backend\User\UserRepositoryInterface;

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
    protected $user;

    public function __construct(UserRepositoryInterface $user)
    {
        $this->middleware('guest', ['except' => 'logout']);
        $this->user = $user;
    }

    public function showRegistrationForm()
    {
        return view('auth.register');
    }

    public function register(Request $request) {

        $data = Input::except(array('_token'));

        $validate= Validator::make(
            $data,
            [
                'name' =>'required|min:5|max:255',
                'email' => 'email|required|unique:user_systems|max:255',
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
            return view('auth.register')->withErrors($validate);
        } else {
            $data_new = [
                'display_name' => $data['display_name'],
                'email' => $data['email'],
                'password' => Hash::make($data['password'])
            ];
            $user = $this->user->store($data_new);

            if(!$user) {
                return Redirect::to('register')->with('errors', 'Xảy ra lỗi trong quá trình đăng ký. Vui lòng thử lại');
            }

            return Redirect::to('login')->with('success', 'Đăng ký tài khoản thành công');
        }
    }






//    public function showRegistrationForm()
//    {
//        return view('auth.register');
//    }
//
//    public function register(Request $request) {
//        $data = $request->only('display_name', 'password', 'password_confirmation', 'email');
//        $validate= Validator::make(
//            $data,
//            [
//                'display_name' =>'required|min:5|max:255',
//                'email' => 'email|required|unique:user_systems|max:255',
//                'password' => 'required|min:6',
//            ],
//
//            [
//                'required'=>':attribute không được để trống',
//                'email'=>':attribute không đúng định dạng',
//                'min'=>':attribute không được nhỏ hơn :min',
//                'unique'=>':attribute đã tồn tại trong hệ thống',
//                'max'=>':attribute không được lớn hơn :max',
//            ],
//            [
//                'display_name' =>'Name',
//                'email' => 'Email',
//                'password' => 'Password',
//            ]
//
//        );
//        if($validate->fails()){
//            $errors = $validate->messages()->all();
//            return Response::json(array(
//                'code' => 0,
//                'data' => $errors,
//                'msg' => 'Dữ liệu không hợp lệ'
//            ));
//        }
//        if($data['password_confirmation'] != $data['password']) {
//            return Response::json(array(
//                'code' => 0,
//                'data' => ['Mật khẩu không trùng khớp'],
//                'msg' => 'Mật khẩu không trùng khớp'
//            ));
//        }
//
//        $data_new = [
//            'display_name' => $data['display_name'],
//            'email' => $data['email'],
//            'password' => Hash::make($data['password'])
//        ];
//
//        $user = $this->user->store($data_new);
//
//        return Response::json(array(
//            'code' => 1,
//            'data' => $user,
//            'msg' => 'Tạo tài khoản thành công'
//        ));
//    }

}
