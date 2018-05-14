<?php

namespace App\Api;

use Illuminate\Database\Eloquent\Model;
use Laravel\Passport\HasApiTokens;

class ResetPassword extends Model
{
    use HasApiTokens;
    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'user_id', 'status', 'code',
    ];
}
