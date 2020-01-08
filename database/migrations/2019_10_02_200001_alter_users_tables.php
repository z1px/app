<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Z1px\App\Models\Users\UsersModel;
use Z1px\App\Models\Users\UsersPassportsModel;


class AlterUsersTables extends Migration
{

    private $users_model = UsersModel::class;
    private $users_passports_model = UsersPassportsModel::class;

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
        if(Schema::hasTable(app($this->users_passports_model)->getTable())
            && Schema::hasTable(app($this->users_model)->getTable())) {
            Schema::table(app($this->users_passports_model)->getTable(), function (Blueprint $table) {
                # 创建外键约束
                $table->foreign('user_id')
                    ->references('id')
                    ->on(app($this->users_model)->getTable())
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
