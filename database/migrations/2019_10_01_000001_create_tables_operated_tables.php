<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;


class CreateTablesOperatedTables extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {

        /**
         * 创建数据库表操作日志表
         */
        Schema::create(app('tables_operated_service')->getTable(), function (Blueprint $table) {

            $table->engine = 'InnoDB'; // 指定表存储引擎 (MySQL).
            $table->charset = 'utf8mb4'; // 指定表的默认字符编码 (MySQL).
            $table->collation = 'utf8mb4_unicode_ci'; // 指定表的默认排序格式 (MySQL).

            $table->bigIncrements('id')->comment('ID');
            $table->string('model', 100)->comment('操作表模型');
            $table->string('table', 50)->index()->comment('操作表名称');
            $table->unsignedBigInteger('tid')->default(0)->comment('操作表ID');
            $table->string('operate')->comment('操作类型：create-新增，delete-删除，update-修改，select-查找，restore-恢复');
            $table->json('before_attr')->nullable()->comment('操作前的数据');
            $table->json('after_attr')->nullable()->comment('操作后的数据');
            $table->json('change_attr')->nullable()->comment('被修改的数据');
            $table->string('route_name', 50)->nullable()->comment('路由名称');
            $table->string('route_action', 100)->nullable()->comment('路由方法');
            $table->string('url', 200)->nullable()->comment('请求地址');
            $table->string('method', 20)->nullable()->comment('请求类型');
            $table->ipAddress('ip')->nullable()->comment('请求IP');
            $table->string('area', 50)->nullable()->comment('IP区域');
            $table->text('user_agent')->nullable()->comment('浏览器信息');
            $table->string('device', 30)->nullable()->comment('设备');
            $table->unsignedBigInteger('user_type')->default(0)->comment('用户类型：1-管理员，2-平台用户');
            $table->unsignedBigInteger('user_id')->default(0)->comment('操作用户ID');
            $table->timestamp('created_at')->useCurrent()->nullable()->comment('创建时间');
            $table->timestamp('updated_at')->useCurrent()->nullable()->comment('更新时间');

            // 创建索引
            $table->index(['table', 'tid']);
            $table->index(['table', 'operate']);

            $table->index(['user_type', 'user_id']);
        });
        app('db')->statement("ALTER TABLE `" . app('tables_operated_service')->getTable() . "` comment '数据库表操作日志表'"); // 表注释
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists(app('tables_operated_service')->getTable()); // 删除数据库表操作日志表
    }
}
