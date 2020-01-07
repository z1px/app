<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;


class AlterAdminsTables extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {

        /**
         * 更新角色-权限关系表
         */
        if(Schema::hasTable(app('roles_permissions_service')->getTable())
            && Schema::hasTable(app('roles_service')->getTable())
            && Schema::hasTable(app('permissions_service')->getTable())) {
            Schema::table(app('roles_permissions_service')->getTable(), function (Blueprint $table) {
                # 创建外键约束
                $table->foreign('role_id')
                    ->references('id')
                    ->on(app('roles_service')->getTable())
                    ->onDelete('cascade');
                $table->foreign('permission_id')
                    ->references('id')
                    ->on(app('permissions_service')->getTable())
                    ->onDelete('cascade');
            });
        }

        /**
         * 更新管理员-角色关系表
         */
        if(Schema::hasTable(app('admins_roles_service')->getTable())
            && Schema::hasTable(app('admins_service')->getTable())
            && Schema::hasTable(app('roles_service')->getTable())) {
            Schema::table(app('admins_roles_service')->getTable(), function (Blueprint $table) {
                # 创建外键约束
                $table->foreign('admin_id')
                    ->references('id')
                    ->on(app('admins_service')->getTable())
                    ->onDelete('cascade');
                $table->foreign('role_id')
                    ->references('id')
                    ->on(app('roles_service')->getTable())
                    ->onDelete('cascade');
            });
        }

        /**
         * 更新管理员-权限关系表
         */
        if(Schema::hasTable(app('admins_permissions_service')->getTable())
            && Schema::hasTable(app('admins_service')->getTable())
            && Schema::hasTable(app('permissions_service')->getTable())) {
            Schema::table(app('admins_permissions_service')->getTable(), function (Blueprint $table) {
                # 创建外键约束
                $table->foreign('admin_id')
                    ->references('id')
                    ->on(app('admins_service')->getTable())
                    ->onDelete('cascade');
                $table->foreign('permission_id')
                    ->references('id')
                    ->on(app('permissions_service')->getTable())
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
