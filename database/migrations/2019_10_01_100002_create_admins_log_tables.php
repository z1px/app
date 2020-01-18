<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Z1px\App\Models\Admins\AdminsBehaviorModel;
use Z1px\App\Models\Admins\AdminsLoginModel;


class CreateAdminsLogTables extends Migration
{

    private $admins_login_model = AdminsLoginModel::class;
    private $admins_behavior_model = AdminsBehaviorModel::class;

    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        /**
         * 创建管理员登录日志表
         */
        Schema::create(app($this->admins_login_model)->getTable(), function (Blueprint $table) {

            $table->engine = 'InnoDB'; // 指定表存储引擎 (MySQL).
            $table->charset = 'utf8mb4'; // 指定表的默认字符编码 (MySQL).
            $table->collation = 'utf8mb4_unicode_ci'; // 指定表的默认排序格式 (MySQL).

            $table->bigIncrements('id')->comment('ID');
            $table->unsignedBigInteger('admin_id')->index()->default(0)->comment('管理员ID');
            $table->string('nickname', 30)->nullable()->comment('昵称');
            $table->string('username', 20)->nullable()->comment('账号');
            $table->string('mobile', 20)->nullable()->comment('手机号');
            $table->string('email', 30)->nullable()->comment('邮箱号');
            $table->string('route_name', 50)->nullable()->comment('路由名称');
            $table->string('route_action', 100)->nullable()->comment('路由方法');
            $table->string('url', 200)->nullable()->comment('请求地址');
            $table->string('method', 20)->nullable()->comment('请求类型');
            $table->ipAddress('ip')->nullable()->comment('请求IP');
            $table->string('area', 50)->nullable()->comment('IP区域');
            $table->text('user_agent')->nullable()->comment('浏览器信息');
            $table->string('device', 30)->nullable()->comment('设备');
            $table->timestamp('created_at')->useCurrent()->nullable()->comment('创建时间');
            $table->timestamp('updated_at')->useCurrent()->nullable()->comment('更新时间');
        });
        app('db')->statement("ALTER TABLE `" . app($this->admins_login_model)->getTable() . "` comment '管理员登录日志表'"); // 表注释

        /**
         * 创建管理员行为日志表
         */
        Schema::create(app($this->admins_behavior_model)->getTable(), function (Blueprint $table) {

            $table->engine = 'InnoDB'; // 指定表存储引擎 (MySQL).
            $table->charset = 'utf8mb4'; // 指定表的默认字符编码 (MySQL).
            $table->collation = 'utf8mb4_unicode_ci'; // 指定表的默认排序格式 (MySQL).

            $table->bigIncrements('id')->comment('ID');
            $table->unsignedBigInteger('admin_id')->index()->default(0)->comment('管理员ID');
            $table->string('title', 30)->nullable()->comment('行为名称');
            $table->string('route_name', 50)->nullable()->comment('路由名称');
            $table->string('route_action', 100)->nullable()->comment('路由方法');
            $table->string('url', 200)->nullable()->comment('请求地址');
            $table->string('method', 20)->nullable()->comment('请求类型');
            $table->json('header')->nullable()->comment('请求头');
            $table->json('request')->nullable()->comment('请求参数');
            $table->longText('response')->nullable()->comment('响应结果');
            $table->ipAddress('ip')->nullable()->comment('请求IP');
            $table->string('area', 50)->nullable()->comment('IP区域');
            $table->text('user_agent')->nullable()->comment('浏览器信息');
            $table->string('device', 30)->nullable()->comment('设备');
            $table->decimal('runtime', 10, 6)->default(0)->comment('运行时间，单位秒');
            $table->timestamp('created_at')->useCurrent()->nullable()->comment('创建时间');
            $table->timestamp('updated_at')->useCurrent()->nullable()->comment('更新时间');
        });
        app('db')->statement("ALTER TABLE `" . app($this->admins_behavior_model)->getTable() . "` comment '管理员行为日志表'"); // 表注释
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists(app($this->admins_login_model)->getTable()); // 删除管理员登录日志表
        Schema::dropIfExists(app($this->admins_behavior_model)->getTable()); // 删除管理员行为日志表
    }
}
