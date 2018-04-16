<?php
namespace App\Repositories\Api\User;

interface UserRepositoryInterface
{

    // Get all users
    public function getAllUser();

    // create new user
    public function store($data);

    // find user by token remember
    public function findUserByToken($token);

}