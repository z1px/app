<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;


class AlterUsersTables extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        /**
         * 更新用户表
         */
        if(Schema::hasTable(app('users_passports_service')->getTable())
            && Schema::hasTable(app('users_service')->getTable())) {
            Schema::table(app('users_passports_service')->getTable(), function (Blueprint $table) {
                # 创建外键约束
                $table->foreign('user_id')
                    ->references('id')
                    ->on(app('users_service')->getTable())
                    ->onDelete('cascade');
            });
        }
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        //
    }
}
