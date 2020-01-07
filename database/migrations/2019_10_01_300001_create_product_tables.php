<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;


class CreateProductTables extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        /**
         * 创建品牌表
         * p_brands
         */
        Schema::create(app('brands_service')->getTable(), function (Blueprint $table) {

            $table->engine = 'InnoDB'; // 指定表存储引擎 (MySQL).
            $table->charset = 'utf8mb4'; // 指定表的默认字符编码 (MySQL).
            $table->collation = 'utf8mb4_unicode_ci'; // 指定表的默认排序格式 (MySQL).

            $table->bigIncrements('id')->comment('ID');
            $table->string('title', 30)->unique()->comment('品牌名称');
//            $table->string('logo', 100)->nullable()->comment('品牌LOGO');
            $table->unsignedBigInteger('file_id')->index()->default(0)->comment('品牌LOGO-关联文件ID');
            $table->string('pinyin', 50)->nullable()->comment('品牌拼音');
            $table->text('description')->nullable()->comment('品牌描述');
            $table->string('website', 200)->nullable()->comment('品牌官网');
            $table->tinyInteger('sort')->default(1)->comment('排序，倒序，数字大的在前面');
            $table->tinyInteger('status')->default(1)->comment('状态：1-正常，2-禁用');
            $table->timestamp('created_at')->useCurrent()->nullable()->comment('创建时间');
            $table->timestamp('updated_at')->useCurrent()->nullable()->comment('更新时间');
            $table->timestamp('deleted_at')->nullable()->comment('软删除时间');
        });
        app('db')->statement("ALTER TABLE `" . app('brands_service')->getTable() . "` comment '品牌表'"); // 表注释

        /**
         * 创建产品分类表
         * p_category
         */
        Schema::create(app('category_service')->getTable(), function (Blueprint $table) {

            $table->engine = 'InnoDB'; // 指定表存储引擎 (MySQL).
            $table->charset = 'utf8mb4'; // 指定表的默认字符编码 (MySQL).
            $table->collation = 'utf8mb4_unicode_ci'; // 指定表的默认排序格式 (MySQL).

            $table->bigIncrements('id')->comment('ID');
            $table->string('title', 30)->unique()->comment('分类名称');
            $table->tinyInteger('status')->default(1)->comment('状态：1-正常，2-禁用');
            $table->unsignedBigInteger('pid')->index()->default(0)->comment('父ID');
            $table->timestamp('created_at')->useCurrent()->nullable()->comment('创建时间');
            $table->timestamp('updated_at')->useCurrent()->nullable()->comment('更新时间');
            $table->timestamp('deleted_at')->nullable()->comment('软删除时间');
        });
        app('db')->statement("ALTER TABLE `" . app('category_service')->getTable() . "` comment '产品分类表'"); // 表注释

        /**
         * 创建产品属性分组表
         * p_attributes
         */
        Schema::create(app('attributes_group_service')->getTable(), function (Blueprint $table) {

            $table->engine = 'InnoDB'; // 指定表存储引擎 (MySQL).
            $table->charset = 'utf8mb4'; // 指定表的默认字符编码 (MySQL).
            $table->collation = 'utf8mb4_unicode_ci'; // 指定表的默认排序格式 (MySQL).

            $table->bigIncrements('id')->comment('ID');
            $table->string('title', 30)->unique()->comment('属性分组名称');
            $table->timestamp('created_at')->useCurrent()->nullable()->comment('创建时间');
            $table->timestamp('updated_at')->useCurrent()->nullable()->comment('更新时间');
            $table->timestamp('deleted_at')->nullable()->comment('软删除时间');
        });
        app('db')->statement("ALTER TABLE `" . app('attributes_group_service')->getTable() . "` comment '产品属性分组表'"); // 表注释

        /**
         * 创建产品属性表
         * p_attributes
         */
        Schema::create(app('attributes_service')->getTable(), function (Blueprint $table) {

            $table->engine = 'InnoDB'; // 指定表存储引擎 (MySQL).
            $table->charset = 'utf8mb4'; // 指定表的默认字符编码 (MySQL).
            $table->collation = 'utf8mb4_unicode_ci'; // 指定表的默认排序格式 (MySQL).

            $table->bigIncrements('id')->comment('ID');
            $table->unsignedBigInteger('attributes_group_id')->index()->default(0)->comment('属性分组ID');
            $table->string('title', 30)->unique()->comment('属性名称');
            $table->tinyInteger('type')->default(0)->comment('属性类型：1-关键属性（单选），2-销售属性（多选），3-非关键属性（自定义）');
            $table->tinyInteger('status')->default(1)->comment('状态：1-正常，2-禁用');
            $table->timestamp('created_at')->useCurrent()->nullable()->comment('创建时间');
            $table->timestamp('updated_at')->useCurrent()->nullable()->comment('更新时间');
            $table->timestamp('deleted_at')->nullable()->comment('软删除时间');
        });
        app('db')->statement("ALTER TABLE `" . app('attributes_service')->getTable() . "` comment '产品属性表'"); // 表注释

        /**
         * 创建分类-属性关系表
         */
        Schema::create(app('category_attributes_service')->getTable(), function (Blueprint $table) {

            $table->engine = 'InnoDB'; // 指定表存储引擎 (MySQL).
            $table->charset = 'utf8mb4'; // 指定表的默认字符编码 (MySQL).
            $table->collation = 'utf8mb4_unicode_ci'; // 指定表的默认排序格式 (MySQL).

            $table->bigIncrements('id')->comment('ID');
            $table->unsignedBigInteger('category_id')->index()->default(0)->comment('分类ID');
            $table->unsignedBigInteger('attribute_id')->index()->default(0)->comment('属性ID');
            $table->tinyInteger('type')->default(0)->comment('属性类型：1-关键属性（单选），2-销售属性（多选），3-非关键属性（自定义）');
            $table->timestamp('created_at')->useCurrent()->nullable()->comment('创建时间');
            $table->timestamp('updated_at')->useCurrent()->nullable()->comment('更新时间');

            // 创建索引
            $table->unique(['category_id', 'attribute_id']);
        });
        app('db')->statement("ALTER TABLE `" . app('category_attributes_service')->getTable() . "` comment '分类-属性关系表'"); // 表注释

        /**
         * 创建产品规格表
         * p_specs
         */
        Schema::create(app('specs_service')->getTable(), function (Blueprint $table) {

            $table->engine = 'InnoDB'; // 指定表存储引擎 (MySQL).
            $table->charset = 'utf8mb4'; // 指定表的默认字符编码 (MySQL).
            $table->collation = 'utf8mb4_unicode_ci'; // 指定表的默认排序格式 (MySQL).

            $table->bigIncrements('id')->comment('ID');
            $table->unsignedBigInteger('attribute_id')->index()->default(0)->comment('属性ID');
            $table->string('title', 30)->unique()->comment('规格名称');
//            $table->string('logo', 100)->nullable()->comment('规格LOGO');
            $table->unsignedBigInteger('file_id')->index()->default(0)->comment('规格LOGO-关联文件ID');
            $table->tinyInteger('status')->default(1)->comment('状态：1-正常，2-禁用');
            $table->timestamp('created_at')->useCurrent()->nullable()->comment('创建时间');
            $table->timestamp('updated_at')->useCurrent()->nullable()->comment('更新时间');
            $table->timestamp('deleted_at')->nullable()->comment('软删除时间');
        });
        app('db')->statement("ALTER TABLE `" . app('specs_service')->getTable() . "` comment '产品规格表'"); // 表注释

        /**
         * 创建产品服务表（与信息表关联）
         * p_services
         */
        Schema::create(app('services_service')->getTable(), function (Blueprint $table) {

            $table->engine = 'InnoDB'; // 指定表存储引擎 (MySQL).
            $table->charset = 'utf8mb4'; // 指定表的默认字符编码 (MySQL).
            $table->collation = 'utf8mb4_unicode_ci'; // 指定表的默认排序格式 (MySQL).

            $table->bigIncrements('id')->comment('ID');
            $table->string('title', 30)->unique()->comment('服务名称');
//            $table->string('logo', 100)->nullable()->comment('服务LOGO');
            $table->unsignedBigInteger('file_id')->index()->default(0)->comment('服务LOGO-关联文件ID');
            $table->mediumText('description')->nullable()->comment('服务描述');
            $table->tinyInteger('status')->default(1)->comment('状态：1-正常，2-禁用');
            $table->timestamp('created_at')->useCurrent()->nullable()->comment('创建时间');
            $table->timestamp('updated_at')->useCurrent()->nullable()->comment('更新时间');
            $table->timestamp('deleted_at')->nullable()->comment('软删除时间');
        });
        app('db')->statement("ALTER TABLE `" . app('services_service')->getTable() . "` comment '产品服务表'"); // 表注释

        /**
         * 创建产品原产地表
         * p_origin
         */

        /**
         * 创建产品供应商表
         * p_supplier
         */

        /**
         * 创建产品发货信息表
         * p_delivery
         */

        /**
         * 创建产品仓库表
         * p_warehouse
         */

        /**
         * 创建产品信息表
         * p_spu
         */
        Schema::create(app('spu_service')->getTable(), function (Blueprint $table) {

            $table->engine = 'InnoDB'; // 指定表存储引擎 (MySQL).
            $table->charset = 'utf8mb4'; // 指定表的默认字符编码 (MySQL).
            $table->collation = 'utf8mb4_unicode_ci'; // 指定表的默认排序格式 (MySQL).

            $table->bigIncrements('id')->comment('ID');
            $table->unsignedBigInteger('brand_id')->index()->default(0)->comment('品牌ID');
            $table->unsignedBigInteger('category_pid')->index()->default(0)->comment('一级分类ID');
            $table->unsignedBigInteger('category_id')->index()->default(0)->comment('二级分类ID');
            $table->string('title', 80)->comment('产品名称');
//            $table->string('image', 100)->nullable()->comment('主显图片');
            $table->unsignedBigInteger('file_id')->index()->default(0)->comment('主显图片-关联文件ID');
            $table->mediumText('description')->nullable()->comment('产品描述');
            $table->tinyInteger('status')->default(1)->comment('状态：1-正常，2-禁用');
            $table->timestamp('created_at')->useCurrent()->nullable()->comment('创建时间');
            $table->timestamp('updated_at')->useCurrent()->nullable()->comment('更新时间');
            $table->timestamp('deleted_at')->nullable()->comment('软删除时间');
        });
        app('db')->statement("ALTER TABLE `" . app('spu_service')->getTable() . "` comment '产品信息表'"); // 表注释

        /**
         * 创建产品信息-属性关系表
         */
        Schema::create(app('spu_attributes_service')->getTable(), function (Blueprint $table) {

            $table->engine = 'InnoDB'; // 指定表存储引擎 (MySQL).
            $table->charset = 'utf8mb4'; // 指定表的默认字符编码 (MySQL).
            $table->collation = 'utf8mb4_unicode_ci'; // 指定表的默认排序格式 (MySQL).

            $table->bigIncrements('id')->comment('ID');
            $table->unsignedBigInteger('spu_id')->index()->default(0)->comment('产品信息ID');
            $table->unsignedBigInteger('attribute_id')->index()->default(0)->comment('属性ID');
            $table->unsignedBigInteger('spec_id')->index()->default(0)->comment('规格ID');
            $table->string('title', 30)->nullable()->comment('规格名称');
            $table->tinyInteger('type')->default(0)->comment('属性类型：1-关键属性（单选），2-销售属性（多选），3-非关键属性（自定义）');
            $table->timestamp('created_at')->useCurrent()->nullable()->comment('创建时间');
            $table->timestamp('updated_at')->useCurrent()->nullable()->comment('更新时间');

            // 创建索引
            $table->unique(['spu_id', 'attribute_id']);
        });
        app('db')->statement("ALTER TABLE `" . app('spu_attributes_service')->getTable() . "` comment '产品信息-属性关系表'"); // 表注释

        /**
         * 创建产品信息-销售属性规格关系表
         */
        Schema::create(app('spu_specs_service')->getTable(), function (Blueprint $table) {

            $table->engine = 'InnoDB'; // 指定表存储引擎 (MySQL).
            $table->charset = 'utf8mb4'; // 指定表的默认字符编码 (MySQL).
            $table->collation = 'utf8mb4_unicode_ci'; // 指定表的默认排序格式 (MySQL).

            $table->bigIncrements('id')->comment('ID');
            $table->unsignedBigInteger('spu_id')->index()->default(0)->comment('产品信息ID');
            $table->unsignedBigInteger('attribute_id')->index()->default(0)->comment('属性ID');
            $table->unsignedBigInteger('spec_id')->index()->default(0)->comment('规格ID');
            $table->string('title', 30)->nullable()->comment('规格名称');
            $table->timestamp('created_at')->useCurrent()->nullable()->comment('创建时间');
            $table->timestamp('updated_at')->useCurrent()->nullable()->comment('更新时间');

            // 创建索引
            $table->unique(['spu_id', 'spec_id']);
        });
        app('db')->statement("ALTER TABLE `" . app('spu_specs_service')->getTable() . "` comment '产品信息-销售属性规格关系表'"); // 表注释

        /**
         * 创建产品信息-服务关系表
         */
        Schema::create(app('spu_services_service')->getTable(), function (Blueprint $table) {

            $table->engine = 'InnoDB'; // 指定表存储引擎 (MySQL).
            $table->charset = 'utf8mb4'; // 指定表的默认字符编码 (MySQL).
            $table->collation = 'utf8mb4_unicode_ci'; // 指定表的默认排序格式 (MySQL).

            $table->bigIncrements('id')->comment('ID');
            $table->unsignedBigInteger('spu_id')->index()->default(0)->comment('产品信息ID');
            $table->unsignedBigInteger('service_id')->index()->default(0)->comment('服务ID');
            $table->timestamp('created_at')->useCurrent()->nullable()->comment('创建时间');
            $table->timestamp('updated_at')->useCurrent()->nullable()->comment('更新时间');

            // 创建索引
            $table->unique(['spu_id', 'service_id']);
        });
        app('db')->statement("ALTER TABLE `" . app('spu_services_service')->getTable() . "` comment '产品信息-服务关系表'"); // 表注释

        /**
         * 创建产品信息-附属图片文件关系表
         */
        Schema::create(app('spu_files_service')->getTable(), function (Blueprint $table) {

            $table->engine = 'InnoDB'; // 指定表存储引擎 (MySQL).
            $table->charset = 'utf8mb4'; // 指定表的默认字符编码 (MySQL).
            $table->collation = 'utf8mb4_unicode_ci'; // 指定表的默认排序格式 (MySQL).

            $table->bigIncrements('id')->comment('ID');
            $table->unsignedBigInteger('spu_id')->index()->default(0)->comment('产品信息ID');
            $table->unsignedBigInteger('file_id')->index()->default(0)->comment('文件ID');
            $table->timestamp('created_at')->useCurrent()->nullable()->comment('创建时间');
            $table->timestamp('updated_at')->useCurrent()->nullable()->comment('更新时间');

            // 创建索引
            $table->unique(['spu_id', 'file_id']);
        });
        app('db')->statement("ALTER TABLE `" . app('spu_files_service')->getTable() . "` comment '产品信息-附属图片文件关系表'"); // 表注释

        /**
         * 创建产品库存表
         * p_sku
         */
        Schema::create(app('sku_service')->getTable(), function (Blueprint $table) {

            $table->engine = 'InnoDB'; // 指定表存储引擎 (MySQL).
            $table->charset = 'utf8mb4'; // 指定表的默认字符编码 (MySQL).
            $table->collation = 'utf8mb4_unicode_ci'; // 指定表的默认排序格式 (MySQL).

            $table->bigIncrements('id')->comment('ID');
            $table->unsignedBigInteger('spu_id')->index()->default(0)->comment('产品信息ID');
            $table->string('sn', 30)->unique()->comment('产品序列号');
            $table->string('title', 120)->comment('库存产品名称');
            $table->decimal('price', 10, 2)->default(0)->comment('产品价格，单位元');
            $table->unsignedInteger('stock')->default(0)->comment('库存');
            $table->tinyInteger('status')->default(1)->comment('状态：1-正常，2-禁用');
            $table->timestamp('created_at')->useCurrent()->nullable()->comment('创建时间');
            $table->timestamp('updated_at')->useCurrent()->nullable()->comment('更新时间');
            $table->timestamp('deleted_at')->nullable()->comment('软删除时间');
        });
        app('db')->statement("ALTER TABLE `" . app('sku_service')->getTable() . "` comment '产品库存表'"); // 表注释

        /**
         * 创建产品库存-销售属性规格关系表
         */
        Schema::create(app('sku_specs_service')->getTable(), function (Blueprint $table) {

            $table->engine = 'InnoDB'; // 指定表存储引擎 (MySQL).
            $table->charset = 'utf8mb4'; // 指定表的默认字符编码 (MySQL).
            $table->collation = 'utf8mb4_unicode_ci'; // 指定表的默认排序格式 (MySQL).

            $table->bigIncrements('id')->comment('ID');
            $table->unsignedBigInteger('spu_id')->index()->default(0)->comment('产品信息ID');
            $table->unsignedBigInteger('sku_id')->index()->default(0)->comment('产品库存ID');
            $table->unsignedBigInteger('attribute_id')->index()->default(0)->comment('属性ID');
            $table->unsignedBigInteger('spec_id')->index()->default(0)->comment('规格ID');
            $table->string('title', 30)->nullable()->comment('规格名称');
            $table->unsignedBigInteger('file_id')->index()->default(0)->comment('规格LOGO-关联文件ID');
            $table->timestamp('created_at')->useCurrent()->nullable()->comment('创建时间');
            $table->timestamp('updated_at')->useCurrent()->nullable()->comment('更新时间');

            // 创建索引
            $table->unique(['sku_id', 'spec_id']);
        });
        app('db')->statement("ALTER TABLE `" . app('sku_specs_service')->getTable() . "` comment '产品库存-销售属性规格关系表'"); // 表注释

        /**
         * 创建产品进销表
         * p_stock
         */
        Schema::create(app('stock_service')->getTable(), function (Blueprint $table) {

            $table->engine = 'InnoDB'; // 指定表存储引擎 (MySQL).
            $table->charset = 'utf8mb4'; // 指定表的默认字符编码 (MySQL).
            $table->collation = 'utf8mb4_unicode_ci'; // 指定表的默认排序格式 (MySQL).

            $table->bigIncrements('id')->comment('ID');
            $table->unsignedBigInteger('spu_id')->index()->default(0)->comment('产品信息ID');
            $table->unsignedBigInteger('sku_id')->index()->default(0)->comment('产品库存ID');
            $table->string('sn', 30)->index()->comment('产品序列号');
            $table->decimal('price', 10, 2)->default(0)->comment('进销价格，单位元');
            $table->unsignedInteger('stock')->default(0)->comment('库存');
            $table->tinyInteger('type')->default(1)->comment('状态：1-进货，2-退货');
            $table->string('remark', 200)->comment('备注');
            $table->timestamp('created_at')->useCurrent()->nullable()->comment('创建时间');
            $table->timestamp('updated_at')->useCurrent()->nullable()->comment('更新时间');
            $table->timestamp('deleted_at')->nullable()->comment('软删除时间');
        });
        app('db')->statement("ALTER TABLE `" . app('stock_service')->getTable() . "` comment '产品进销表'"); // 表注释

        /**
         * 创建产品评论表
         * p_comments
         */

        // 物流公司信息表
        // 购物车

        // 用户收货地址表

        // 产品收藏表

        // 地区表

        // 广告

        // 导航

        // 支付方式

        // 产品图片与文件资源管理中间表

        // 商品标签
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::disableForeignKeyConstraints(); // 关闭外键约束
        Schema::dropIfExists(app('brands_service')->getTable()); // 删除品牌表
        Schema::dropIfExists(app('category_service')->getTable()); // 删除产品分类表
        Schema::dropIfExists(app('attributes_group_service')->getTable()); // 删除产品属性分类表
        Schema::dropIfExists(app('attributes_service')->getTable()); // 删除产品属性表
        Schema::dropIfExists(app('specs_service')->getTable()); // 删除产品规格表
        Schema::dropIfExists(app('category_attributes_service')->getTable()); // 删除分类-属性表
        Schema::dropIfExists(app('spu_files_service')->getTable()); // 删除产品信息-附属图片文件关系表
        Schema::dropIfExists(app('spu_services_service')->getTable()); // 删除产品信息-服务关系表
        Schema::dropIfExists(app('spu_attributes_service')->getTable()); // 删除产品信息-属性关系表
        Schema::dropIfExists(app('spu_specs_service')->getTable()); // 删除产品信息-规格关系表
        Schema::dropIfExists(app('spu_service')->getTable()); // 删除产品信息表
        Schema::dropIfExists(app('services_service')->getTable()); // 删除创建产品服务表
        Schema::dropIfExists(app('sku_specs_service')->getTable()); // 删除产品库存-规格关系表
        Schema::dropIfExists(app('sku_service')->getTable()); // 删除产品库存表
        Schema::dropIfExists(app('stock_service')->getTable()); // 删除产品进货表
        Schema::enableForeignKeyConstraints(); // 开启外键约束
    }
}
