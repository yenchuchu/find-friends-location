<?php

namespace App\Http\Controllers\Frontend;

use App\Frontend\User;
use App\Http\Controllers\Controller;
use App\Repositories\Api\User\UserRepositoryInterface;
use Firebase\JWT\JWT;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\Hash;
use Validator;
use Response;
use Illuminate\Foundation\Auth\AuthenticatesUsers;

class LoginController extends Controller
{
    use AuthenticatesUsers;
    protected $user;

    public function __construct(UserRepositoryInterface $user)
    {
        $this->middleware('guest', ['except' => 'logout']);
        $this->user = $user;
    }

    /**
     * Login by email and password
     */
    public function login(Request $request) {
        $data = $request->only('password', 'email');

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
            $errors = $validate->messages()->all();
            return Response::json(array(
                'code' => 0,
                'data' => $errors,
                'msg' => 'Dữ liệu không hợp lệ'
            ));
        }
        $user = User::where('email', '=', $data['email'])->first();
        if(!$user) {
            return Response::json(array(
                'code' => 2,
                'msg' => 'Email chưa tồn tại'
            ));
        }
        if(!Hash::check($data['password'], $user->password)){
            return Response::json(array(
                'code' => 2,
                'msg' => 'Mật khẩu sai'
            ));
        }

        $user->last_login = date('Y-m-d H:i:s');
        $user->ip_address = $this->getIpAddress();
        $user->user_token = $this->generatorToken($user->id, $user->ip_address);
        $user->save();

        return Response::json(array(
            'code' => 1,
            'data' => $user,
            'msg' => 'Đăng nhập thành công'
        ));
    }

    public function generatorToken($useId, $ip)
    {
        $aki = 'eSXT7lyPBHMqUxYKSYB1jvNFCEZZfs109FAxbC0f';
        return JWT::encode(array('user' => $useId, 'token' => uniqid(), 'ip' => $ip), $aki);
    }

    public function getIpAddress()
    {
        return \Request::ip();
    }

    public function getUserInfo(Request $request){
        $user = JWTAuth::toUser($request->token);
        return response()->json(['result' => $user]);
    }

}
