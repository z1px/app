<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;


class AlterProductTables extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        /**
         * 更新产品属性表
         */
        if(Schema::hasTable(app('attributes_service')->getTable())
            && Schema::hasTable(app('attributes_group_service')->getTable())) {
            Schema::table(app('attributes_service')->getTable(), function (Blueprint $table) {
                # 创建外键约束
                $table->foreign('attributes_group_id')
                    ->references('id')
                    ->on(app('attributes_group_service')->getTable())
                    ->onDelete('cascade');
            });
        }

        /**
         * 更新分类-属性关系表
         */
        if(Schema::hasTable(app('category_attributes_service')->getTable())
            && Schema::hasTable(app('category_service')->getTable())
            && Schema::hasTable(app('attributes_service')->getTable())) {
            Schema::table(app('category_attributes_service')->getTable(), function (Blueprint $table) {
                # 创建外键约束
                $table->foreign('category_id')
                    ->references('id')
                    ->on(app('category_service')->getTable())
                    ->onDelete('cascade');
                $table->foreign('attribute_id')
                    ->references('id')
                    ->on(app('attributes_service')->getTable())
                    ->onDelete('cascade');
            });
        }

        /**
         * 更新产品规格表
         */
        if(Schema::hasTable(app('specs_service')->getTable())
            && Schema::hasTable(app('attributes_service')->getTable())) {
            Schema::table(app('specs_service')->getTable(), function (Blueprint $table) {
                # 创建外键约束
                $table->foreign('attribute_id')
                    ->references('id')
                    ->on(app('attributes_service')->getTable())
                    ->onDelete('cascade');
            });
        }

        /**
         * 更新产品信息表
         */
        if(Schema::hasTable(app('spu_service')->getTable())
            && Schema::hasTable(app('brands_service')->getTable())
            && Schema::hasTable(app('category_service')->getTable())) {
            Schema::table(app('spu_service')->getTable(), function (Blueprint $table) {
                # 创建外键约束
                $table->foreign('brand_id')
                    ->references('id')
                    ->on(app('brands_service')->getTable())
                    ->onDelete('cascade');
                $table->foreign('category_id')
                    ->references('id')
                    ->on(app('category_service')->getTable())
                    ->onDelete('cascade');
            });
        }

        /**
         * 更新产品信息-属性关系表
         */
        if(Schema::hasTable(app('spu_attributes_service')->getTable())
            && Schema::hasTable(app('spu_service')->getTable())
            && Schema::hasTable(app('attributes_service')->getTable())) {
            Schema::table(app('spu_attributes_service')->getTable(), function (Blueprint $table) {
                # 创建外键约束
                $table->foreign('spu_id')
                    ->references('id')
                    ->on(app('spu_service')->getTable())
                    ->onDelete('cascade');
                $table->foreign('attribute_id')
                    ->references('id')
                    ->on(app('attributes_service')->getTable())
                    ->onDelete('cascade');
            });
        }

        /**
         * 更新产品信息-规格关系表
         */
        if(Schema::hasTable(app('spu_specs_service')->getTable())
            && Schema::hasTable(app('spu_service')->getTable())
            && Schema::hasTable(app('attributes_service')->getTable())
            && Schema::hasTable(app('specs_service')->getTable())) {
            Schema::table(app('spu_specs_service')->getTable(), function (Blueprint $table) {
                # 创建外键约束
                $table->foreign('spu_id')
                    ->references('id')
                    ->on(app('spu_service')->getTable())
                    ->onDelete('cascade');
                $table->foreign('attribute_id')
                    ->references('id')
                    ->on(app('attributes_service')->getTable())
                    ->onDelete('cascade');
                $table->foreign('spec_id')
                    ->references('id')
                    ->on(app('specs_service')->getTable())
                    ->onDelete('cascade');
            });
        }

        /**
         * 更新产品信息-服务关系表
         */
        if(Schema::hasTable(app('spu_services_service')->getTable())
            && Schema::hasTable(app('spu_service')->getTable())
            && Schema::hasTable(app('services_service')->getTable())) {
            Schema::table(app('spu_services_service')->getTable(), function (Blueprint $table) {
                # 创建外键约束
                $table->foreign('spu_id')
                    ->references('id')
                    ->on(app('spu_service')->getTable())
                    ->onDelete('cascade');
                $table->foreign('service_id')
                    ->references('id')
                    ->on(app('services_service')->getTable())
                    ->onDelete('cascade');
            });
        }

        /**
         * 更新产品信息-附属图片文件关系表
         */
        if(Schema::hasTable(app('spu_files_service')->getTable())
            && Schema::hasTable(app('spu_service')->getTable())) {
            Schema::table(app('spu_files_service')->getTable(), function (Blueprint $table) {
                # 创建外键约束
                $table->foreign('spu_id')
                    ->references('id')
                    ->on(app('spu_service')->getTable())
                    ->onDelete('cascade');
            });
        }

        /**
         * 更新产品库存表
         */
        if(Schema::hasTable(app('sku_service')->getTable())
            && Schema::hasTable(app('spu_service')->getTable())) {
            Schema::table(app('sku_service')->getTable(), function (Blueprint $table) {
                # 创建外键约束
                $table->foreign('spu_id')
                    ->references('id')
                    ->on(app('spu_service')->getTable())
                    ->onDelete('cascade');
            });
        }

        /**
         * 更新产品库存-规格关系表
         */
        if(Schema::hasTable(app('sku_specs_service')->getTable())
            && Schema::hasTable(app('spu_service')->getTable())
            && Schema::hasTable(app('sku_service')->getTable())
            && Schema::hasTable(app('attributes_service')->getTable())
            && Schema::hasTable(app('specs_service')->getTable())) {
            Schema::table(app('sku_specs_service')->getTable(), function (Blueprint $table) {
                # 创建外键约束
                $table->foreign('spu_id')
                    ->references('id')
                    ->on(app('spu_service')->getTable())
                    ->onDelete('cascade');
                $table->foreign('sku_id')
                    ->references('id')
                    ->on(app('sku_service')->getTable())
                    ->onDelete('cascade');
                $table->foreign('attribute_id')
                    ->references('id')
                    ->on(app('attributes_service')->getTable())
                    ->onDelete('cascade');
                $table->foreign('spec_id')
                    ->references('id')
                    ->on(app('specs_service')->getTable())
                    ->onDelete('cascade');
            });
        }

        /**
         * 更新产品进货表
         */
        if(Schema::hasTable(app('stock_service')->getTable())
            && Schema::hasTable(app('spu_service')->getTable())
            && Schema::hasTable(app('sku_service')->getTable())) {
            Schema::table(app('stock_service')->getTable(), function (Blueprint $table) {
                # 创建外键约束
                $table->foreign('spu_id')
                    ->references('id')
                    ->on(app('spu_service')->getTable())
                    ->onDelete('cascade');
                $table->foreign('sku_id')
                    ->references('id')
                    ->on(app('sku_service')->getTable())
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
