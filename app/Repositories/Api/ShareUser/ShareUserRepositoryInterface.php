<?php
namespace App\Repositories\Api\User;

interface ShareUserRepositoryInterface
{
    // get list friends sharing by auth
    public function getListFriendsSentByUserId($user_id);

    // get list friends share own for auth
    public function getListFriendsRecieveByUserId($user_id);

    //check exist record user receive share
    public function checkHasSharingReceiveById($id_user_receive, $id_user_send);

    //create record user share
    public function store($data);
}