<?php
namespace App\Repositories\Api\User;

interface UserRepositoryInterface
{
    // Get all users
    public function getAllUser();

    /************************************/
    /** CREATE - UPDATE - DELETE USER **/

    // create new user
    public function store($data);

    /** END CREATE - UPDATE - DELETE USER
     ************************************/
    /** GET INFOMATIONS USER BY CONDITIONS **/

    //find total_info by user_id_generate
    public function getInfoUserByGenarate($key_genarate);

    //find total_info by token
    public function getInfoUserByToken($token);

    /** END GET INFOMATIONS USER BY CONDITIONS
    ************************************/

    /** FIND USER ID BY CONDITIONS **/

    // find user id by token remember
    public function findUserIdByToken($token);

    /** END FIND USER ID BY CONDITIONS
    ************************************/

    /** FIND USER BY CONDITIONS **/

    // find user by token remember
    public function findUserByToken($token);

    // find user by token remember
    public function findUserById($user_id);

    // find user by Email
    public function findUserByEmail($email);

    //find user by input search
    public function findUserByInputSearch($input_search);

    /** END FIND USER BY CONDITIONS
    ************************************/
    /** FORGET PASSWORD **/

    // send Email in order to change password
    public function sendEmailToChangePassword($email);

    // create code in order to change password
    public function createExpire($user_id, $code_rand, $status);

    // find code change password by user_id, status and code
    public function findExpire($user_id, $code, $status);

    // check password and update time when login
    public function checkPasswordWhenLogin($input_password, $user);

    /** END FORGET PASSWORD
     ************************************/

}