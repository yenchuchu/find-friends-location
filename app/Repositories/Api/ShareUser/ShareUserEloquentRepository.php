<?php

namespace App\Repositories\Api\ShareUser;

use App\Api\ShareUser;
use App\Repositories\Api\User\ShareUserRepositoryInterface;
use App\Repositories\RepositoryAbstract;
use Illuminate\Http\Request;
use Config;

class ShareUserEloquentRepository extends RepositoryAbstract implements ShareUserRepositoryInterface
{
// Class thực hiện tất cả các function được định nghĩa trong UserRepositoryInterface và thừa kế các function
//được định nghĩa trong RepositoryAbstract

    /**
     * get model
     * @return string
     */
    public function getModel()
    {
        return ShareUser::class;
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
     * Tạm chưa dùng đến
     * get list friends sharing by user id auth
     * - function giống getListFriendsByUserId nhưng lấy theo cách chia ra
     * @return \Illuminate\Database\Eloquent\Collection|static[]
     */
    public function getListFriendsByUserIdDetails($user_id)
    {
        $status_send_location = Config::get('constants.share_user.status.send_location');
        $status_sharing_location = Config::get('constants.share_user.status.sharing_location');
        $status_required_send_locations = Config::get('constants.share_user.status.required_send_locations');

        // Những user được auth share location - auth là người share
        $send_locations = $this->_model->select('user_id_send', 'user_id_receive', 'message', 'status', 'info_user_receive')
            ->where(['user_id_send' => $user_id])
            ->get();

        if (!$send_locations || count($send_locations) < 1) {
            $users['send_locations_to_others']['send_locations'] = [];
            $users['send_locations_to_others']['sharing'] = [];
            $users['send_locations_to_others']['required_send_locations'] = [];
        } else {
            $users['send_locations_to_others']['send_locations'] = $send_locations->filter(function ($value, $key) {
                $item_users = [];
                if ($value->status == $status_send_location) { // auth share own location to other users  and waiting other users accept
                    $item_users[] = $value;
                }
                return $item_users;
            })->toArray();

            $users['send_locations_to_others']['sharing'] = $send_locations->filter(function ($value, $key) {
                $item_users = [];
                if ($value->status == $status_sharing_location) { // auth is sharing own location to other users
                    $item_users[] = $value;
                }
                return $item_users;
            })->toArray();

            $users['send_locations_to_others']['required_send_locations'] = $send_locations->filter(function ($value, $key) {
                $item_users = [];
                if ($value->status == $status_required_send_locations) { // auth sent required to other users in order to share their location for auth and waiting other users accept
                    $item_users[] = $value;
                }
                return $item_users;
            })->toArray();
        }

        // Những user share location cho auth - auth là người nhận
        $receive_locations = $this->_model->select('user_id_send', 'user_id_receive', 'message', 'status', 'info_user_receive')
            ->where(['user_id_receive' => $user_id])
            ->get();

        if (!$receive_locations || count($receive_locations) < 1) {
            $users['others_send_location_to_me']['send_locations'] = [];
            $users['others_send_location_to_me']['sharing'] = [];
            $users['others_send_location_to_me']['required_send_locations'] = [];
        } else {
            $users['others_send_location_to_me']['send_locations'] = $receive_locations->filter(function ($value, $key) {
                $item_users = [];
                if ($value->status == $status_send_location) { // auth share own location to other users  and waiting other users accept
                    $item_users[] = $value;
                }
                return $item_users;
            })->toArray();

            $users['others_send_location_to_me']['sharing'] = $receive_locations->filter(function ($value, $key) {
                $item_users = [];
                if ($value->status == $status_sharing_location) { // auth is sharing own location to other users
                    $item_users[] = $value;
                }
                return $item_users;
            })->toArray();

            $users['others_send_location_to_me']['required_send_locations'] = $receive_locations->filter(function ($value, $key) {
                $item_users = [];
                if ($value->status == $status_required_send_locations) { // auth sent required to other users in order to share their location for auth and waiting other users accept
                    $item_users[] = $value;
                }
                return $item_users;
            })->toArray();
        }

        return ['me_send_location' => $users['send_locations_to_others'], 'me_receive_location' => $users['others_send_location_to_me']];
    }

    /**
     * get list friends sharing by user id auth
     * @return \Illuminate\Database\Eloquent\Collection|static[]
     */
    public function getListFriendsByUserId($user_id)
    {
        // Những user được auth share location - auth là người share
        $send_locations = $this->_model->select('user_id_send', 'user_id_receive', 'message', 'status', 'info_user_receive')
            ->where(['user_id_send' => $user_id])
            ->get();

        if (!$send_locations || count($send_locations) < 1) {
            $users['send_locations_to_others'] = [];
        } else {
            $users['send_locations_to_others'] = $send_locations->toArray();
        }

        // Những user share location cho auth - auth là người nhận
        $receive_locations = $this->_model->select('user_id_send', 'user_id_receive', 'message', 'status', 'info_user_receive')
            ->where(['user_id_receive' => $user_id])
            ->get();

        if (!$receive_locations || count($receive_locations) < 1) {
            $users['others_send_location_to_me'] = [];
        } else {
            $users['others_send_location_to_me'] = $receive_locations->toArray();
        }

        return ['me_send_location' => $users['send_locations_to_others'], 'me_receive_location' => $users['others_send_location_to_me']];
    }

    /**
     * check exist record user receive share
     * @param $id_user_receive
     */
    public function checkHasSharingReceiveById($id_user_receive, $id_user_send)
    {
        $user_share = $this->_model->where(['user_id_send' => $id_user_send, 'user_id_receive' => $id_user_receive])->first();

        if (!$user_share) {
            return false;
        }

        return $user_share;
    }

    /**
     * create record user share
     * @param $data
     */
    public function store($data)
    {
        $user_share = $this->_model->create($data);

        if (!$user_share) {
            return false;
        }

        return $user_share;
    }
}