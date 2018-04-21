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
            $table->text('info_user_receive')
                ->comment('type: json ~ string; concat: avarta, display_name, phone, email, address');
            $table->tinyInteger('status')
                ->comment('Trạng thái: 0- chờ user_id_receive chấp nhận; 1 - đang cùng kết nối; 2 - chờ user_id_send chấp nhận share ')
                ->default(0);
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
