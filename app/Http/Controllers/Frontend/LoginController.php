<?php

namespace App\Http\Controllers\Frontend;

use App\Frontend\ResetPassword;
use App\Frontend\User;
use App\Http\Controllers\Controller;
use App\Repositories\Api\User\UserRepositoryInterface;
use Carbon\Carbon;
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
    protected $reset_password;

    public function __construct(UserRepositoryInterface $user)
    {
        $this->middleware('guest', ['except' => 'logout']);
        $this->user = $user;
    }

    /**
     * send data from body
     * Login by email and password
     */
    public function login(Request $request) {
        $allRequest = $request->all();
        if(!isset($allRequest['password']) || !isset($allRequest['email'])) {
            return Response::json(array(
                'code' => 0,
                'data' => ['Thiếu dữ liệu gửi lên'],
                'msg' => 'Thiếu dữ liệu gửi lên'
            ));
        }
        $data = [
            'password' => $allRequest['password'],
            'email' => $allRequest['email']
        ];

        $validate= Validator::make(
            $data,
            [
                'email' => 'email|required|max:255',
                'password' => 'required|min:6',
            ],
            [
                'required'=>':attribute Không được để trống',
                'email'=>':attribute Không đúng định dạng',
                'min'=>':attribute Không được nhỏ hơn :min kí tự',
                'unique'=>':attribute đã tồn tại trong hệ thống',
                'max'=>':attribute Không được lớn hơn :max kí tự',
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

        $user = $this->user->findUserByEmail($data['email']);
        if(!$user) {
            return Response::json(array(
                'code' => 2,
                'msg' => 'Email chưa được đăng ký'
            ));
        }

        $check_password = $this->user->checkPasswordWhenLogin($data['password'], $user);
        if($check_password == 1){
            return Response::json(array(
                'code' => 1,
                'data' => $user,
                'msg' => 'Đăng nhập thành công'
            ));
        } else if($check_password == 3) {
            return Response::json(array(
                'code' => 2,
                'msg' => 'Mật khẩu sai'
            ));
        } else {
            return Response::json(array(
                'code' => 2,
                'msg' => 'Xảy ra lỗi trong quá trình đăng nhập. Vui lòng thử lại'
            ));
        }
    }

    public function getUserInfo(Request $request){
        $user = JWTAuth::toUser($request->token);
        return response()->json(['result' => $user]);
    }

    /**
     * Học viên gửi email để lấy lại password
     * nếu status = 1: mã code đó đã được sử dụng hoặc không sử dụng được
     * status = 0: mã code đúng và có thể sử dụng
     * @return mixed
     */
    public function requiredResetPassword(Request $request)
    {
        try{
            $data = $request->all();
            if(!isset($data['email']) || $data['email'] == null || $data['email'] == '') {
                return Response::json(array(
                    'code' => 0,
                    'msg' => 'Email không được để trống'
                ));
            }
            $user = $this->user->findUserByEmail($data['email']);
            if(!$user){
                return Response::json(array(
                    'code' => 2,
                    'data' => [],
                    'mgs' => 'Email không đúng. Vui lòng nhập email bạn đăng ký tài khoản'
                ));
            }

            $user_id = $user->id;

            // tìm kiếm user đã có bản ghi nào chưa sử dụng không
            $code_rand = mt_rand(10000000, 99999999);
            $expire = $this->user->findExpireByUserIdAndStatus($user_id, 0);
            if(!$expire){
                // Chưa có bản ghi expire. Tạo bản ghi mới cho học viên. và gửi email cho học viên
                return $this->createExpireResetPassword($user_id, $code_rand, 0, $user->display_name);
            } else {
                $check_update_expire = $expire->update([
                    'status' => 1
                ]);
                if(!$check_update_expire) {
                    return array(
                        'code' => 2,
                        'data' => [],
                        'msg' => 'Đã xảy ra lỗi. Vui lòng thử lại.'
                    );
                }
                return $this->createExpireResetPassword($user_id, $code_rand, 0, $user->display_name);
            }
        }catch(Exception $e){
            return Response::json(array(
                'code' => 2,
                'data' => [],
                'msg' => 'Đã xảy ra lỗi. Vui lòng thử lại.'
            ));
        }
    }

    /**
     * Tạo 1 bản ghi mới cho học viên lấy lại mật khẩu
     * @param $user_id
     * @param $code_rand
     * @return array
     */
    public function createExpireResetPassword($user_id, $code_rand, $status, $user_display) {
        // Chưa có bản ghi expire. Tạo bản ghi mới cho học viên.
        $check_create_expire = $this->user->createExpire($user_id, $code_rand, $status);

        if(!$check_create_expire) {
            return Response::json(array(
                'code' => 2,
                'data' => [],
                'msg' => 'Đã xảy ra lỗi. Vui lòng thử lại.'
            ));
        } else {
            // send email cho học viên khi tạo bản ghi thành công.
//            $email_branch = getEmailByBranch();
//            $branch = ENV('ENV_BRAND');
//            if ($branch == 'en') {
//                $sender_email = 'noreply@elight.edu.vn';
//                $name_from = 'Elight Education';
//                $hotline_phone = '096 114 8634';
//            } else if ($branch == 'jp') {
//                $sender_email = 'noreply@akira.edu.vn';
//                $name_from = 'Akira Education';
//                $hotline_phone = '096 239 9945';
//            }

            $sender_email = 'hienctt.akira@gmail.com';
            $name_from = 'Find Locations';
            $hotline_phone = '0164 719 6261';
//            $url = ENV('URL_RESET_PASSWORD').$code_rand.'-'.$user_id;
            $url = '/localhost/'.$code_rand.'-'.$user_id;
//            // gửi email cho học viên xác nhận
//            $send_email_to_user = $this->email->sendEmailResetPassword($url, 'emails.account.for-get-password', $sender_email, $name_from, Input::get('email'), $name_from.'_Lấy lại mật khẩu', $user_display, $hotline_phone);
//            if(!$send_email_to_user) {
//                return array(
//                    'code' => 0,
//                    'msg' => 'Tìm kiếm bị lỗi. Vui lòng thử lại.'
//                );
//            }
            return Response::json(array(
                'code' => 1,
                'mgs' => 'Bạn vui kiểm tra Email để lấy lại mật khẩu nhé.'
            ));
        }
    }

    /**
     * check url và Thay đổi lại mật khẩu
     * nếu thay đổi mật khẩu sau 24h yêu cầu lấy lại mật khẩu thì phải gửi lại yêu cầu
     * @return mixed
     */
    public function resetPassword(Request $request) {
        $data = $request->all();
        if(!isset($data['code']) || !isset($data['password'])  || !isset($data['re_password']) || !$data['code'] || !$data['password'] || !$data['re_password']) {
            return Response::json(array(
                'code' => 0,
                'mgs' => 'Thiếu dữ liệu gửi lên. Vui lòng thử lại'
            ));
        }

        if($data['password'] != $data['re_password']) {
            return Response::json(array(
                'code' => 0,
                'mgs' => 'Mật khẩu không khớp. Vui lòng thử lại'
            ));
        }
        $convert_code = explode("-",$data['code']); // data[code] bao gồm user_id + code
        $user_id = $convert_code[1];
        $user_code = $convert_code[0];

        $expire = $this->user->findExpire($user_id, $user_code, 0);

        if(!$expire) {
            return Response::json(array(
                'code' => 2,
                'mgs' => 'Đã xảy ra lỗi trong quá trình xác thực. Vui lòng gửi lại yêu cầu quên mật khẩu. '
            ));
        }

        $now = Carbon::createFromFormat('Y-m-d H:i:s', Carbon::now());
        $created_at = Carbon::createFromFormat('Y-m-d H:i:s', $expire->created_at);
        $check_hours = $created_at->diffInHours($now);

        if($check_hours > 24) {
            // đã hết hạn đổi mật khẩu. cập nhật lại status = 1. và yêu cầu gửi lại yêu cầu đổi lại mật khẩu
            $check_update_expire = $expire->update(['status' => 1]);
            if(!$check_update_expire) {
                return Response::json(array(
                    'code' => 2,
                    'mgs' => 'Thay đổi mật khẩu bị lỗi. Vui lòng thử lại'
                ));
            }

            return Response::json(array(
                'code' => 2,
                'mgs' => 'Bạn chỉ được lấy lại mật khẩu trong vòng 24h từ khi gửi yêu cầu quên mật khẩu. Vui lòng gửi lại yêu cầu quên mật khẩu.'
            ));
        } else {
            // đang còn hạn đổi mật khẩu
            $user = $this->user->findUserById($user_id);
            if(!$user) {
                return Response::json(array(
                    'code' => 2,
                    'mgs' => 'Thay đổi mật khẩu bị lỗi. Vui lòng thử lại'
                ));
            }
            $check_update_pw = $user->update([
                'password' => Hash::make(sha1($data['password']))
            ]);

            if(!$check_update_pw) {
                return Response::json(array(
                    'code' => 2,
                    'mgs' => 'Thay đổi mật khẩu bị lỗi. Vui lòng thử lại'
                ));
            }

            $check_update_expire = $expire->update(['status'=> 1]);
            if(!$check_update_expire) {
                return Response::json(array(
                    'code' => 2,
                    'mgs' => 'Thay đổi mật khẩu bị lỗi. Vui lòng thử lại'
                ));
            }

            return Response::json(array(
                'code' => 1,
                'mgs' => 'Cập nhật thành công'
            ));
        }
    }

}
