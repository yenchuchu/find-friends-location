<?php
namespace App\Http\Controllers\Frontend;

use App\Http\Controllers\Controller;
use App\Repositories\Api\ShareUser\ShareUserEloquentRepository;
use App\Repositories\Api\User\ShareUserRepositoryInterface;
use App\Repositories\Api\User\UserEloquentRepository;
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
    protected $share_user;

    public function __construct() {
        $this->user = new UserEloquentRepository();
        $this->share_user = new ShareUserEloquentRepository();
    }

    /**
     * method: GET - truyền user_token trong header
     * @param Request $request
     * @return mixed
     */
    public function getListFriends(Request $request) {
        $headers = apache_request_headers();
        if(!$headers || !isset($headers['user_token']) || $headers['user_token'] == '' || $headers['user_token'] == null) {
            return Response::json(array(
                'code' => 0,
                'msg' => 'Bạn chưa đăng nhập'
            ));
        }
        $user_id = $this->user->findUserIdByToken($headers['user_token']);

        if(!$user_id) {
            return Response::json(array(
                'code' => 2,
                'msg' => 'Bạn chưa đăng nhập'
            ));
        }

        $users = $this->share_user->getListFriendsByUserId($user_id);

        if(!$users) {
            return Response::json(array(
                'code' => 1,
                'data' => null,
                'msg' => 'Không có dữ liệu'
            ));
        }

        return Response::json(array(
            'code' => 1,
            'data' => $users,
            'msg' => ''
        ));
    }

    /**
     * Medthod: GET - truyền user_token trong header
     * Lấy thông tin phone, address, email, display_name, avatar - type JSON
     */
    public function profile() {
        $headers = apache_request_headers();

        if(!$headers || !isset($headers['user_token']) || $headers['user_token'] == '' || $headers['user_token'] == null) {
            return Response::json(array(
                'code' => 0,
                'msg' => 'Bạn chưa đăng nhập'
            ));
        }

        $user = $this->user->getInfoUserByToken($headers['user_token']);

        if(!$user) {
            return Response::json(array(
                'code' => 2,
                'msg' => 'Đã xảy ra lỗi. Vui lòng đăng nhập lại.'
            ));
        }
        return Response::json(array(
            'code' => 1,
            'data' => $user,
            'msg' => 'Lấy dữ liệu thành công'
        ));
    }

    /**
     * Medthod: GET - truyền user_token trong header
     *  tìm kiếm user
     */
    public function searchUsers(Request $request) {
        $data = $request->all();
        $headers = apache_request_headers();

        if(!$headers || !isset($headers['user_token']) || $headers['user_token'] == '' || $headers['user_token'] == null) {
            return Response::json(array(
                'code' => 0,
                'msg' => 'Bạn chưa đăng nhập'
            ));
        }

        if(!$data || !isset($data['input_search']) || !$data['input_search']) {
            return Response::json(array(
                'code' => 0,
                'msg' => 'Đã xảy ra lỗi. Vui lòng tải lại trang.'
            ));
        }

        $users = $this->user->findUserByInputSearch($data['input_search']);

        if(!$users) {
            return Response::json(array(
                'code' => 1,
                'data' => [],
                'msg' => 'Không tìm thấy.'
            ));
        }

        return Response::json(array(
            'code' => 1,
            'data' => $users,
            'msg' => 'Tìm kiếm thành công'
        ));
    }



}

?>