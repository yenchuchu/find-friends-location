<?php

namespace App\Repositories\Api\User;

use App\Api\ResetPassword;
use App\Api\User;
use App\Repositories\RepositoryAbstract;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Firebase\JWT\JWT;

class UserEloquentRepository extends RepositoryAbstract implements UserRepositoryInterface
{
// Class thực hiện tất cả các function được định nghĩa trong UserRepositoryInterface và thừa kế các function
//được định nghĩa trong RepositoryAbstract

    /**
     * get model
     * @return string
     */
    public function getModel()
    {
        return User::class;
    }

    /**
     * get all user
     * @return \Illuminate\Database\Eloquent\Collection|static[]
     */
    public function getAllUser()
    {
        return $this->_model->all();
    }

    /**
     * Get info users
     * @return \Illuminate\Database\Eloquent\Collection|static[]
     */
    public function getUserByEmail($email)
    {
        $user = $this->_model->where('email', '=', $email)->first();
        if(!$user) {
            return false;
        }

        return $user;
    }

    /**
     * create new user
     * @param $data
     */
    public function store($data)
    {
        $data['ip_address'] = \Request::ip();
        $data['user_id_generate'] = '1';
        $user = $this->_model->create($data);

        if (!$user) {
            return false;
        }

        return $user;
    }

    /**
     * find total_info by user_id_generate
     * @param $token
     * @return bool
     */
    public function getInfoUserByGenarate($key_genarate)
    {
        $user = $this->_model->select('id', 'total_info')->where('user_id_generate', $key_genarate)->first();

        if (!$user) {
            return false;
        }

        return $user;
    }

    /**
     * find total_info by token
     * @param $token
     * @return bool
     */
    public function getInfoUserByToken($token)
    {
        $user = $this->_model->select('id', 'avatar', 'display_name', 'phone', 'email', 'address', 'total_info')
        ->where('user_token', $token)->first();

        if (!$user) {
            return false;
        }

        if($user->total_info == null) {
            $total_info_array = [
                'display_name' => $user->display_name,
                'email' => $user->email,
                'phone' => $user->phone,
                'address' => $user->address,
                'avatar' => $user->avatar
            ];
            $user->total_info = json_encode($total_info_array);

            $check_save = $user->save();

            if(!$check_save) {
                return false;
            }
        }

        return $user;
    }

    /**
     * find user ID by token remember
     * @param $token
     * @return bool
     */
    public function findUserIdByToken($token)
    {
        $user = $this->_model->select('id')->where('user_token', $token)->first();

        if (!$user) {
            return false;
        }

        return $user->id;
    }

    /**
     * find some infomation of user by token remember
     * @param $token
     * @return bool
     */
    public function findUserIdAndGenerateByToken($token)
    {
        $user = $this->_model->select('id', 'user_id_generate')->where('user_token', $token)->first();

        if (!$user) {
            return false;
        }

        return ['user_id' => $user->id, 'code_genarate' => $user->user_id_generate];
    }

    /**
     * find user by token remember
     * @param $token
     * @return bool
     */
    public function findUserByToken($token)
    {
        $user = $this->_model->where('user_token', $token)->first();

        if (!$user) {
            return false;
        }

        return $user;
    }

    /**
     * find user by user_id
     * @param $user_id
     * @return bool
     */
    public function findUserById($user_id)
    {
        $user = $this->_model->where('id', $user_id)->first();

        if (!$user) {
            return false;
        }

        return $user;
    }

    /**
     * find user by email
     * @param $token
     * @return bool
     */
    public function findUserByEmail($email)
    {
        $user = $this->_model->where('email', $email)->first();

        if (!$user) {
            return false;
        }

        return $user;
    }

    /**
     * find user by input search
     * @param $token
     * @return bool
     */
    public function findUserByInputSearch($input_search)
    {
        $users = $this->_model->select('id', 'display_name', 'avatar')
            ->where('total_info_string', 'like', '%'.$input_search.'%')
            ->get();

        if (!$users) {
            return false;
        }

        return $users;
    }

    /**
     * send email inoder to change password
     * @param $token
     * @return bool
     */
    public function sendEmailToChangePassword($email)
    {
        $user = $this->_model->where('email', $email)->first();

        if (!$user) {
            return false;
        }

        return $user;
    }

    /**
     * Tìm kiếm mã đổi mật khẩu của user bằng user id và status
     * @param $user_id
     * @param $status
     * @return bool
     */
    public function findExpireByUserIdAndStatus($user_id, $status)
    {
        $expire = ResetPassword::where([
            'user_id' => $user_id,
            'status' => $status
        ])->first();

        if (!$expire) {
            return false;
        } else {
            return $expire;
        }
    }

    /**
     * Tạo 1 bản ghi mới
     * @param $user_id
     * @param $code_rand
     * @param $status
     * @return bool|static
     */
    public function createExpire($user_id, $code_rand, $status)
    {
        $expire = ResetPassword::create([
            'user_id' => $user_id,
            'code' => $code_rand,
            'status' => $status // 0: mặc định chưa được sử dụng; 1: mặc định đã được sử dụng
        ]);

        if (!$expire) {
            return false;
        } else {
            return $expire;
        }
    }

    /**
     * tìm kiếm bản ghi có user id, status và code theo điều kiện
     * @param $user_id
     * @param $code
     * @param $status
     * @return bool|static
     */
    public function findExpire($user_id, $code, $status)
    {
        $expire = ResetPassword::where([
            'user_id' => $user_id,
            'code' => $code,
            'status' => $status
        ])->first();

        if (!$expire) {
            return false;
        } else {
            return $expire;
        }
    }

    /**
     * kiểm tra mật khẩu và cập nhật thời gian khi login
     * check password and update time when login
     * @param $input_password - mật khẩu nhập vào
     * @param $user - user auth
     * @return bool|static
     */
    public function checkPasswordWhenLogin($input_password, $user)
    {
        if(!Hash::check(sha1($input_password), $user->password)){
            return 3;
        }

        $user->last_login = date('Y-m-d H:i:s');
        $user->ip_address = $this->getIpAddress();
        $user->user_token = $this->generatorToken($user->id, $user->ip_address);
        $check_save = $user->save();
        if(!$check_save) {
            return 2;
        }

        return 1;
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