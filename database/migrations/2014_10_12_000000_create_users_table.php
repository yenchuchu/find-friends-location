<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateUsersTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('users', function (Blueprint $table) {
            $table->increments('id');
            $table->string('user_id_generate')->nullable();
            $table->string('email')->unique();
            $table->string('display_name');
            $table->string('address')->nullable();
            $table->string('phone')->nullable();
            $table->string('ip_address')->nullable();
            $table->string('password');
            $table->string('user_token')->nullable();
            $table->date('last_login')->nullable();
            $table->string('avatar')->nullable();
            $table->string('total_info')
                ->comment('type: json ~ string; concat: avarta, display_name, phone, email, address');
            $table->string('total_info_string')
                ->comment('type: string; concat: avarta - display_name - phone - email - address');
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
