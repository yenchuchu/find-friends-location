<?php
namespace App\Repositories\Web\User;

use App\Web\UserSystem;
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
        return UserSystem::class;
    }

    /**
     * Lấy tất cả users
     * @return \Illuminate\Database\Eloquent\Collection|static[]
     */
    public function getAllUser() {
        return $this->_model->all();
    }

    /**
     * Tạo 1 user mới
     * @param $data
     */
    public function store($data) {
        $data['ip_address'] = \Request::ip();
        $user = $this->_model->create($data);

        if(!$user) {
            return false;
        }

        return $user;
    }
}