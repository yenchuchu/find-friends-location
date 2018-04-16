<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateUsersSystemTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('users_systems', function (Blueprint $table) {
            $table->increments('id');
            $table->string('display_name');
            $table->string('password');
            $table->string('email')->unique();
            $table->string('address')->nullable();
            $table->string('phone')->nullable();
            $table->string('role_id')->nullable();
            $table->string('ip_address')->nullable();
            $table->string('user_token')->nullable();
            $table->date('last_login')->nullable();
            $table->string('avatar')->nullable();
            $table->string('total_info')->comment('concat address, email, phone')->nullable();
            $table->tinyInteger('status')->default(1)->comment('1- đang hoạt động, 2- bị tạm dừng hoạt động');
            $table->rememberToken();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('users');
    }
}
