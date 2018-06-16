<?php
namespace App\Http\Controllers\Api;

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
use App\Api\User;
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
     * Lấy danh sách những người được auth chia sẽ location
     * @param Request $request
     * @return mixed
     */
    public function getListFriendsSent(Request $request) {
        $headers = apache_request_headers();
        if(!$headers || !isset($headers['Authorization']) || $headers['Authorization'] == '' || $headers['Authorization'] == null) {
            return Response::json(array(
                'code' => 0,
                'msg' => 'Bạn chưa đăng nhập'
            ));
        }
        $user_id = $this->user->findUserIdByToken($headers['Authorization']);

        if(!$user_id) {
            return Response::json(array(
                'code' => 2,
                'msg' => 'Bạn chưa đăng nhập'
            ));
        }

        $users = $this->share_user->getListFriendsSentByUserId($user_id);

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
     * method: GET - truyền user_token trong header
     * Lấy danh sách những người share location cho auth
     * @param Request $request
     * @return mixed
     */
    public function getListFriendsRecieve(Request $request) {
        $headers = apache_request_headers();
        if(!$headers || !isset($headers['Authorization']) || $headers['Authorization'] == '' || $headers['Authorization'] == null) {
            return Response::json(array(
                'code' => 0,
                'msg' => 'Bạn chưa đăng nhập'
            ));
        }
        $user_id = $this->user->findUserIdByToken($headers['Authorization']);

        if(!$user_id) {
            return Response::json(array(
                'code' => 2,
                'msg' => 'Bạn chưa đăng nhập'
            ));
        }

        $users = $this->share_user->getListFriendsRecieveByUserId($user_id);

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

        if(!$headers || !isset($headers['Authorization']) || $headers['Authorization'] == '' || $headers['Authorization'] == null) {
            return Response::json(array(
                'code' => 0,
                'msg' => 'Bạn chưa đăng nhập'
            ));
        }

        $user = $this->user->getInfoUserByToken($headers['Authorization']);

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

        if(!$headers || !isset($headers['Authorization']) || $headers['Authorization'] == '' || $headers['Authorization'] == null) {
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