<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateShareUsersTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('share_users', function (Blueprint $table) {
            $table->increments('id');
            $table->string('user_id_send');
            $table->string('user_id_receive');
            $table->string('message')->comment('share location with message')->nullable();
            $table->tinyInteger('status')
                ->comment('Trạng thái: 1- chờ user_id_receive chấp nhận; 2 - đang cùng kết nối; 3 - đã ngắt kết nối ')->nullable();
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
