<?php

namespace App\Http\Controllers\Backend;

use App\User as UserSystem;
use App\Repositories\Backend\User\UserRepositoryInterface;
use Firebase\JWT\JWT;

use Illuminate\Foundation\Auth\AuthenticatesUsers;
use Illuminate\Support\Facades\Input;
use Illuminate\Support\Facades\Redirect;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\Facades\Validator;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Hash;
use Illuminate\Http\Request;
use Illuminate\Support\MessageBag;
use Response;
use Illuminate\Support\Facades\Auth;

class LoginController extends Controller
{
    use AuthenticatesUsers;
    protected $user;

    public function __construct(UserRepositoryInterface $user)
    {
        $this->middleware('guest', ['except' => 'logout']);
        $this->user = $user;
    }

    public function showLoginForm() {

        return view('auth.login');
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
            return Redirect::to('login')->withErrors($validate);
        } else {
            $data = Input::except((array('_token')));
            $user = UserSystem::where('email', '=', $data['email'])->first();

            if(Auth::attempt($data)) {
                // Tìm thấy tài khoản
                if(!$user) {
                    return Redirect::to('login')->with('errors', 'Xảy ra lỗi trong quá trình đăng nhập');
                }
                $user->last_login = date('Y-m-d H:i:s');
                $user->ip_address = $this->getIpAddress();
                $user->user_token = $this->generatorToken($user->id, $user->ip_address);
                $user->save();

                return Redirect::to('home');
            } else {
                if(!$user) {
                    $validate = ['email' => 'Email không tồn tại'];
                }
                if(!Hash::check($data['password'], $user->password)){
                    $validate = ['password' => 'Mật khẩu sai'];
                }

                return Redirect::to('login')->withErrors($validate);
            }
        }

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
}
