<?php
namespace App\Repositories\Api\User;

interface ShareUserRepositoryInterface
{
    // get list friends sharing by user id auth
    public function getListFriendsByUserId($user_id);

    //check exist record user receive share
    public function checkHasSharingReceiveById($id_user_receive, $id_user_send);

    //create record user share
    public function store($data);
}