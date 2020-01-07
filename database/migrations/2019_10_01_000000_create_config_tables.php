<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;


class CreateConfigTables extends Migration
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
        Schema::create(app('config_service')->getTable(), function (Blueprint $table) {

            $table->engine = 'InnoDB'; // 指定表存储引擎 (MySQL).
            $table->charset = 'utf8mb4'; // 指定表的默认字符编码 (MySQL).
            $table->collation = 'utf8mb4_unicode_ci'; // 指定表的默认排序格式 (MySQL).

            $table->bigIncrements('id')->comment('ID');
            $table->string('title', 30)->unique()->comment('标题');
            $table->string('key', 30)->unique()->comment('配置健');
            $table->string('value', 120)->nullable()->comment('配置值');
            $table->string('brief', 200)->nullable()->comment('描述');
            $table->string('input', 200)->comment('表单操作类型，text：文本，select：下拉框，radio：单选框，checkbox：复选框');
            $table->json('values')->nullable()->comment('默认可选值');
            $table->tinyInteger('type')->default(0)->comment('配置类型：1-基本配置');
            $table->tinyInteger('status')->default(1)->comment('状态：1-正常，2-禁用');
            $table->timestamp('created_at')->useCurrent()->nullable()->comment('创建时间');
            $table->timestamp('updated_at')->useCurrent()->nullable()->comment('更新时间');
        });
        app('db')->statement("ALTER TABLE `" . app('config_service')->getTable() . "` comment '通用配置表'"); // 表注释
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists(app('config_service')->getTable()); // 删除通用配置表
    }
}
