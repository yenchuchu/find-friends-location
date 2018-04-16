<?php
namespace App\Repositories\Api\User;

use App\Frontend\User;
use App\Repositories\RepositoryAbstract;
use Illuminate\Http\Request;

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
    public function getAllUser() {
        return $this->_model->all();
    }

    /**
     * create new user
     * @param $data
     */
    public function store($data) {
        $data['ip_address'] = \Request::ip();
        $data['user_id_generate'] = '1';
        $user = $this->_model->create($data);

        if(!$user) {
            return false;
        }

        return $user;
    }

    /**
     * find user by token remember
     * @param $token
     * @return bool
     */
    public function findUserByToken($token) {
        $user = $this->_model->where('user_token', $token)->first();

        if(!$user) {
            return false;
        }

        return $user;
    }
}