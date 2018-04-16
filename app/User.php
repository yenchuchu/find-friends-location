<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Laravel\Passport\HasApiTokens;

/**
 * User Quản lý các admin
 * Class User
 * @package App
 */
class User extends Model
{
    use HasApiTokens;
    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $table = 'users';
    protected $fillable = [
        'display_name', 'email', 'password', 'address', 'phone', 'total_info', 'status', 'last_login', 'ip_address',
        'user_token', 'avatar',
    ];

    /**
     * The attributes that should be hidden for arrays.
     *
     * @var array
     */
    protected $hidden = [
        'password', 'remember_token',
    ];
}
