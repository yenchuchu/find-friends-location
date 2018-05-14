<?php
namespace App\Repositories\Web\User;

interface UserRepositoryInterface
{

    // Get all users
    public function getAllUser();

    // Tạo user mới
    public function store($data);

}