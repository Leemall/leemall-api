<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateAdminsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('admins', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->string('user_name',100)->unique()->comment('账号');
            $table->string('email',100)->nullable()->unique()->comment('邮箱');
            $table->string('password')->comment('密码');
            $table->string('name',30)->nullable()->comment('姓名');
            $table->string('phone',20)->nullable()->comment('电话');
            $table->string('avatar')->nullable()->comment('头像');
            $table->timestamp('last_login')->nullable()->comment('最后登录时间');
            $table->string('last_login_ip',30)->nullable()->comment('最后登录IP');
            $table->tinyInteger('enabled')->default(1)->comment('是否可用');
            $table->timestamps();
            $table->softDeletes();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('admins');
    }
}
