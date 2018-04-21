<?php

namespace App\Frontend;

use Illuminate\Database\Eloquent\Model;
use Laravel\Passport\HasApiTokens;

class User extends Model
{
    use HasApiTokens;
    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'display_name', 'email', 'password', 'address', 'phone', 'total_info', 'status', 'last_login', 'ip_address',
        'user_token', 'avatar', 'user_id_generate', 'total_info_string',
    ];

    /**
     * The attributes that should be hidden for arrays.
     *
     * @var array
     */
    protected $hidden = [
        'password', 'remember_token', 'total_info', 'status', 'ip_address', 'last_login', 'total_info_string',
    ];
}
