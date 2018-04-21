<?php

namespace App\Http\Controllers\Frontend;

use App\Frontend\User;
use App\Http\Controllers\Controller;
use App\Repositories\Api\User\UserRepositoryInterface;

use Illuminate\Support\Facades\Input;
use Illuminate\Support\Facades\Redirect;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Hash;
use Illuminate\Http\Request;
use Auth;
use Response;

class RegisterController extends Controller
{
    protected $user;

    public function __construct(UserRepositoryInterface $user)
    {
        $this->middleware('guest', ['except' => 'logout']);
        $this->user = $user;
    }

    public function register(Request $request) {
        $data = $request->only('display_name', 'password', 're_password', 'email');
        $validate= Validator::make(
            $data,
            [
                'display_name' =>'required|min:5|max:255',
                'email' => 'email|required|unique:users|max:255',
                'password' => 'required|min:6',
                're_password' => 'required',
            ],
            [
                'required'=>':attribute không được để trống',
                'email'=>':attribute không đúng định dạng',
                'min'=>':attribute không được nhỏ hơn :min kí tự',
                'unique'=>':attribute đã tồn tại trong hệ thống',
                'max'=>':attribute không được lớn hơn :max kí tự',
            ],
            [
                'display_name' =>'Name',
                'email' => 'Email',
                'password' => 'Password',
                're_password' => 'Confirm password', // mật khẩu xác nhận
            ]
        );
        if($validate->fails()){
            $errors = $validate->messages()->all();
            return Response::json(array(
                'code' => 0,
                'data' => $errors,
                'msg' => 'Dữ liệu không hợp lệ'
            ));
        }
        if($data['re_password'] != $data['password']) {
            return Response::json(array(
                'code' => 0,
                'data' => ['Mật khẩu không trùng khớp'],
                'msg' => 'Mật khẩu không trùng khớp'
            ));
        }

        $data_new = [
            'display_name' => $data['display_name'],
            'email' => $data['email'],
            'password' => Hash::make(sha1($data['password'])),
            'total_info' => $data['email'],
            'total_info_string' => $data['email'],
        ];

        $user = $this->user->store($data_new);

        if(!$user) {
            return Response::json(array(
                'code' => 2,
                'msg' => 'Xảy ra lỗi trong quá trình tạo tài khoản'
            ));
        }

        $total_info_json = [
            'display_name' => $user->display_name,
            'email' => $user->email,
            'phone' => $user->phone,
            'address' => $user->address,
            'avatar' => $user->avatar
        ];

        $total_info_string = [
            'display_name' => $user->display_name,
            'email' => $user->email,
            'phone' => $user->phone,
            'address' => $user->address,
        ];

        $user->total_info = json_encode($total_info_json);
        $user->total_info_string = join($total_info_string);
        $user->user_id_generate = $user->id.$this->generateRandomString(15);
        $check_save = $user->save();

        if(!$check_save) {
            return Response::json(array(
                'code' => 2,
                'msg' => 'Xảy ra lỗi trong quá trình tạo tài khoản'
            ));
        }

        return Response::json(array(
            'code' => 1,
            'data' => $user,
            'msg' => 'Tạo tài khoản thành công'
        ));
    }

    function generateRandomString($length) {
        $characters = '0123456789abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ';
        $charactersLength = strlen($characters);
        $randomString = '';
        for ($i = 0; $i < $length; $i++) {
            $randomString .= $characters[rand(0, $charactersLength - 1)];
        }
        return $randomString;
    }
}
