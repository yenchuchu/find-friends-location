<?php
namespace App\Http\Controllers\Frontend;

use App\Http\Controllers\Controller;
use App\Repositories\Api\User\UserRepositoryInterface;
use Firebase\JWT\JWT;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
//use Tymon\JWTAuth\JWTAuth;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\Hash;
use Validator;
use Response;
use App\Frontend\User;
use Illuminate\Support\Facades\Input;

class UserController extends Controller {
    protected $user;

    public function __construct(UserRepositoryInterface $user) {
        $this->user = $user;
    }

//    get all users
    public function index() {
        $data = Input::only('user_token');
        if($data['user_token'] == '' || $data['user_token'] == null) {
            return Response::json(array(
                'code' => 0,
                'msg' => 'Bạn chưa đăng nhập1'
            ));
        }

        $user = $this->user->findUserByToken($data['user_token']);

        if(!$user) {
            return Response::json(array(
                'code' => 0,
                'msg' => 'Bạn chưa đăng nhập2'
            ));
        }

        $users = $this->user->getAllUser();

        if(!$users) {
            return Response::json(array(
                'code' => 1,
                'data' => [],
                'msg' => 'Không có dữ liệu'
            ));
        }

        return Response::json(array(
            'code' => 1,
            'data' => $users,
            'msg' => ''
        ));
    }

}

?>