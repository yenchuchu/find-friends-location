<?php
namespace App\Http\Controllers\Frontend;

use App\Http\Controllers\Controller;
use App\Repositories\Api\ShareUser\ShareUserEloquentRepository;
use App\Repositories\Api\User\ShareUserRepositoryInterface;
use App\Repositories\Api\User\UserEloquentRepository;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Response;
use Config;

class ShareUserController extends Controller {
    protected $share_user, $user;

    public function __construct() {
        $this->share_user = new ShareUserEloquentRepository();
        $this->user = new UserEloquentRepository();
    }

    /**
     * Tạo bản ghi khi ai đó yêu cầu hoặc gửi location
     * @param Request $request
     * @return mixed
     */
    public function createSharing(Request $request) {
        $data = $request->all();
        $headers = apache_request_headers();

        if(!$headers || !isset($headers['Authorization']) || !$headers['Authorization']) {
            return Response::json(array(
                'code' => 0,
                'msg' => 'Bạn chưa đăng nhập'
            ));
        }

        if(!isset($data['user_id_receive_generate']) || !isset($data['status']) || !isset($data['message']) || !$data['user_id_receive_generate'] || !$data['status'] == null || !$data['message'] || $data['status'] == Config::get('constants.share_user.status.sharing_location') ) {
            // $data['status'] = 0 hoặc 2. 1 là khi người kia đã accept
            return Response::json(array(
                'code' => 0,
                'msg' => 'Đã xảy ra lỗi. Vui lòng tải lại trang.'
            ));
        }

        $user_id = $this->user->findUserIdByToken($headers['Authorization']);
        $info_user_receive = $this->user->getInfoUserByGenarate($data['user_id_receive_generate']);

        if(!$info_user_receive || !$user_id || $user_id == $info_user_receive->id) {
            return Response::json(array(
                'code' => 2,
                'msg' => 'Đã xảy ra lỗi. Vui lòng đăng nhập lại.'
            ));
        }

        $check_exist_sharing = $this->share_user->checkHasSharingReceiveById($info_user_receive->id, $user_id);
        if($check_exist_sharing) { // đã tồn tại bản ghi
            if($check_exist_sharing->status == 1) {
                return Response::json(array(
                    'code' => 1,
                    'msg' => 'Các bạn đã kết nối với nhau.'
                ));
            }
            return Response::json(array(
                'code' => 1,
                'msg' => 'Vui lòng đợi xác nhận.'
            ));
        }

        $data_create = [
            'user_id_send' => $user_id,
            'user_id_receive' => $info_user_receive->id,
            'message' => $data['message'] ,
            'info_user_receive' => $info_user_receive->total_info,
            'status' => $data['status']
        ];

        $check_create = $this->share_user->store($data_create);

        if(!$check_create) {
            return Response::json(array(
                'code' => 2,
                'msg' => 'Đã xảy ra lỗi'
            ));
        }

        return Response::json(array(
            'code' => 1,
            'msg' => 'Gửi yêu cầu thành công'
        ));
    }

    /**
     * Thay đổi trạng thái share: 0 -> 1 hoặc 2 -> 1.
     * @param Request $request
     */
    public function changeStatusSharing(Request $request) {
        $data = $request->all();
        $headers = apache_request_headers();

        $status = Config::get('constants.share_user.status.sharing_location');

        if(!$headers || !isset($headers['Authorization']) || !$headers['Authorization']) {
            return Response::json(array(
                'code' => 0,
                'msg' => 'Bạn chưa đăng nhập'
            ));
        }

        if(!isset($data['user_id_receive_generate']) || !$data['user_id_receive_generate']) {
            return Response::json(array(
                'code' => 0,
                'msg' => 'Đã xảy ra lỗi. Vui lòng tải lại trang.'
            ));
        }

        $user_id = $this->user->findUserIdByToken($headers['Authorization']);
        $info_user_receive = $this->user->getInfoUserByGenarate($data['user_id_receive_generate']);

        if(!$info_user_receive || !$user_id || $user_id == $info_user_receive->id) {
            return Response::json(array(
                'code' => 2,
                'msg' => 'Đã xảy ra lỗi. Vui lòng đăng nhập lại.'
            ));
        }

        $check_exist_sharing = $this->share_user->checkHasSharingReceiveById($info_user_receive->id, $user_id);
        if(!$check_exist_sharing) {
            return Response::json(array(
                'code' => 2,
                'msg' => 'Đã xảy ra lỗi. Vui lòng tải lại trang.'
            ));
        }

        if($check_exist_sharing->status == Config::get('constants.share_user.status.sharing_location')) {
            return Response::json(array(
                'code' => 1,
                'msg' => 'Các bạn đã kết nối với nhau.'
            ));
        }

        $check_exist_sharing->status = $status;
        $check_save = $check_exist_sharing->save();

        if(!$check_save) {
            return Response::json(array(
                'code' => 2,
                'msg' => 'Đã xảy ra lỗi. Vui lòng tải lại trang.'
            ));
        }

        return Response::json(array(
            'code' => 1,
            'msg' => 'Chia sẻ thành công.'
        ));
    }

    /**
     * Xóa bản ghi share trong database
     * @param Request $request
     */
    public function deleteSharing(Request $request) {
        $data = $request->all();
        $headers = apache_request_headers();
        $status = Config::get('constants.share_user.status.sharing_location');

        if(!$headers || !isset($headers['Authorization']) || !$headers['Authorization']) {
            return Response::json(array(
                'code' => 0,
                'msg' => 'Bạn chưa đăng nhập'
            ));
        }

        if(!isset($data['user_id_receive_generate']) || !$data['user_id_receive_generate']) {
            return Response::json(array(
                'code' => 0,
                'msg' => 'Đã xảy ra lỗi. Vui lòng tải lại trang.'
            ));
        }

        $user_id = $this->user->findUserIdByToken($headers['Authorization']);
        $info_user_receive = $this->user->getInfoUserByGenarate($data['user_id_receive_generate']);

        if(!$info_user_receive || !$user_id || $user_id == $info_user_receive->id) {
            return Response::json(array(
                'code' => 2,
                'msg' => 'Đã xảy ra lỗi. Vui lòng đăng nhập lại.'
            ));
        }

        $check_exist_sharing = $this->share_user->checkHasSharingReceiveById($info_user_receive->id, $user_id);
        if(!$check_exist_sharing) {
            return Response::json(array(
                'code' => 2,
                'msg' => 'Đã xảy ra lỗi. Vui lòng tải lại trang.'
            ));
        }

        $check_delete = $check_exist_sharing->delete();

        if(!$check_delete) {
            return Response::json(array(
                'code' => 2,
                'msg' => 'Đã xảy ra lỗi. Vui lòng tải lại trang.'
            ));
        }

        return Response::json(array(
            'code' => 1,
            'msg' => 'Xóa thành công.'
        ));
    }

}

?>