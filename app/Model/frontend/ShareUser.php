<?php

namespace App\Frontend;

use Illuminate\Database\Eloquent\Model;
use Laravel\Passport\HasApiTokens;

class ShareUser extends Model
{
    use HasApiTokens;
    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
       'id', 'user_id_send', 'user_id_receive', 'message', 'status', 'info_user_receive'
    ];
}
