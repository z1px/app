<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;


class CreateAdminsTables extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        /**
         * 创建后台管理员账号表
         */
        Schema::create(app('admins_service')->getTable(), function (Blueprint $table) {

            $table->engine = 'InnoDB'; // 指定表存储引擎 (MySQL).
            $table->charset = 'utf8mb4'; // 指定表的默认字符编码 (MySQL).
            $table->collation = 'utf8mb4_unicode_ci'; // 指定表的默认排序格式 (MySQL).

            // 创建表字段
            $table->bigIncrements('id')->comment('ID');
            $table->string('nickname', 30)->nullable()->comment('昵称');
            $table->string('username', 20)->unique()->nullable()->comment('账号');
            $table->string('mobile', 20)->unique()->nullable()->comment('手机号');
            $table->string('email', 30)->unique()->nullable()->comment('邮箱号');
//            $table->string('avatar', 100)->nullable()->comment('头像');
            $table->unsignedBigInteger('file_id')->index()->default(0)->comment('头像-关联文件ID');
            $table->string('password', 120)->nullable()->comment('密码');
            $table->string('google_secret', 30)->nullable()->comment('谷歌验证器密钥');
            $table->string('access_token', 100)->nullable()->comment('访问令牌');
            $table->tinyInteger('status')->default(1)->comment('账号状态：1-正常，2-禁用');
            $table->tinyInteger('login_failure')->default(0)->comment('连续登录失败次数');
            $table->timestamp('login_at')->nullable()->comment('最后登录时间');
            $table->timestamp('created_at')->useCurrent()->nullable()->comment('创建时间');
            $table->timestamp('updated_at')->useCurrent()->nullable()->comment('更新时间');
            $table->timestamp('deleted_at')->nullable()->comment('软删除时间');
        });
        app('db')->statement("ALTER TABLE `" . app('admins_service')->getTable() . "` comment '后台管理员账号表'"); // 表注释

        /**
         * 创建权限表
         */
        Schema::create(app('permissions_service')->getTable(), function (Blueprint $table) {

            $table->engine = 'InnoDB'; // 指定表存储引擎 (MySQL).
            $table->charset = 'utf8mb4'; // 指定表的默认字符编码 (MySQL).
            $table->collation = 'utf8mb4_unicode_ci'; // 指定表的默认排序格式 (MySQL).

            $table->bigIncrements('id')->comment('ID');
            $table->string('title', 30)->unique()->comment('权限名称');
            $table->string('route_name', 50)->unique()->comment('路由名称');
            $table->string('route_action', 100)->unique()->comment('路由方法');
            $table->string('icon', 60)->nullable()->comment('图标，支持font-awesome等');
            $table->tinyInteger('sort')->default(1)->comment('排序，倒序，数字大的在前面');
            $table->tinyInteger('show')->default(2)->comment('是否作为菜单展示：1-展示，2-隐藏');
            $table->tinyInteger('status')->default(1)->comment('状态：1-正常，2-禁用');
            $table->unsignedBigInteger('pid')->index()->default(0)->comment('父ID');
            $table->timestamp('created_at')->useCurrent()->nullable()->comment('创建时间');
            $table->timestamp('updated_at')->useCurrent()->nullable()->comment('更新时间');
        });
        app('db')->statement("ALTER TABLE `" . app('permissions_service')->getTable() . "` comment '权限表'"); // 表注释

        /**
         * 创建角色表
         */
        Schema::create(app('roles_service')->getTable(), function (Blueprint $table) {

            $table->engine = 'InnoDB'; // 指定表存储引擎 (MySQL).
            $table->charset = 'utf8mb4'; // 指定表的默认字符编码 (MySQL).
            $table->collation = 'utf8mb4_unicode_ci'; // 指定表的默认排序格式 (MySQL).

            $table->bigIncrements('id')->comment('ID');
            $table->string('title', 30)->unique()->comment('角色名称');
            $table->tinyInteger('status')->default(1)->comment('状态：1-正常，2-禁用');
            $table->timestamp('created_at')->useCurrent()->nullable()->comment('创建时间');
            $table->timestamp('updated_at')->useCurrent()->nullable()->comment('更新时间');
        });
        app('db')->statement("ALTER TABLE `" . app('roles_service')->getTable() . "` comment '角色表'"); // 表注释

        /**
         * 创建角色-权限关系表
         */
        Schema::create(app('roles_permissions_service')->getTable(), function (Blueprint $table) {

            $table->engine = 'InnoDB'; // 指定表存储引擎 (MySQL).
            $table->charset = 'utf8mb4'; // 指定表的默认字符编码 (MySQL).
            $table->collation = 'utf8mb4_unicode_ci'; // 指定表的默认排序格式 (MySQL).

            $table->bigIncrements('id')->comment('ID');
            $table->unsignedBigInteger('role_id')->index()->default(0)->comment('角色ID');
            $table->unsignedBigInteger('permission_id')->index()->default(0)->comment('权限ID');
            $table->timestamp('created_at')->useCurrent()->nullable()->comment('创建时间');
            $table->timestamp('updated_at')->useCurrent()->nullable()->comment('更新时间');

            // 创建索引
            $table->unique(['role_id', 'permission_id']);
        });
        app('db')->statement("ALTER TABLE `" . app('roles_permissions_service')->getTable() . "` comment '角色-权限关系表'"); // 表注释

        /**
         * 创建管理员-角色关系表
         */
        Schema::create(app('admins_roles_service')->getTable(), function (Blueprint $table) {

            $table->engine = 'InnoDB'; // 指定表存储引擎 (MySQL).
            $table->charset = 'utf8mb4'; // 指定表的默认字符编码 (MySQL).
            $table->collation = 'utf8mb4_unicode_ci'; // 指定表的默认排序格式 (MySQL).

            $table->bigIncrements('id')->comment('ID');
            $table->unsignedBigInteger('admin_id')->index()->default(0)->comment('管理员ID');
            $table->unsignedBigInteger('role_id')->index()->default(0)->comment('角色ID');
            $table->timestamp('created_at')->useCurrent()->nullable()->comment('创建时间');
            $table->timestamp('updated_at')->useCurrent()->nullable()->comment('更新时间');

            // 创建索引
            $table->unique(['admin_id', 'role_id']);
        });
        app('db')->statement("ALTER TABLE `" . app('admins_roles_service')->getTable() . "` comment '管理员-角色关系表'"); // 表注释

        /**
         * 创建管理员-权限关系表
         */
        Schema::create(app('admins_permissions_service')->getTable(), function (Blueprint $table) {

            $table->engine = 'InnoDB'; // 指定表存储引擎 (MySQL).
            $table->charset = 'utf8mb4'; // 指定表的默认字符编码 (MySQL).
            $table->collation = 'utf8mb4_unicode_ci'; // 指定表的默认排序格式 (MySQL).

            $table->bigIncrements('id')->comment('ID');
            $table->unsignedBigInteger('admin_id')->index()->default(0)->comment('管理员ID');
            $table->unsignedBigInteger('permission_id')->index()->default(0)->comment('权限ID');
            $table->timestamp('created_at')->useCurrent()->nullable()->comment('创建时间');
            $table->timestamp('updated_at')->useCurrent()->nullable()->comment('更新时间');

            // 创建索引
            $table->unique(['admin_id', 'permission_id']);
        });
        app('db')->statement("ALTER TABLE `" . app('admins_permissions_service')->getTable() . "` comment '管理员-权限关系表'"); // 表注释
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
//        Schema::disableForeignKeyConstraints(); // 关闭外键约束
        Schema::dropIfExists(app('roles_permissions_service')->getTable()); // 删除角色-权限关系表
        Schema::dropIfExists(app('admins_roles_service')->getTable()); // 删除管理员-角色关系表
        Schema::dropIfExists(app('admins_permissions_service')->getTable()); // 删除管理员-权限关系表
        Schema::dropIfExists(app('permissions_service')->getTable()); // 删除权限表
        Schema::dropIfExists(app('roles_service')->getTable()); // 删除角色表
        Schema::dropIfExists(app('admins_service')->getTable()); // 删除管理员表
//        Schema::enableForeignKeyConstraints(); // 开启外键约束
    }
}
