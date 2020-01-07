<?php

/*
|--------------------------------------------------------------------------
| Admin Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| contains the "web" middleware group. Now create something great!
|
*/

// 在 「App\Http\Controllers\Admin」 命名空间下的控制器

Route::any('login', "IndexController@login")->name('login'); // 登录

// demo
Route::any('table', 'DemoController@table')->name('table'); // table列表数据展示示例
Route::any('form', 'DemoController@form')->name('form'); // form表单添加或修改数据示例
Route::any('jump', 'DemoController@jump')->name('public.jump'); // 跳转页面

Route::middleware('admin.auth')->group(function () {

    Route::any('/', "IndexController@index")->name('index'); // 首页
    Route::any('logout', "IndexController@logout")->name('logout'); // 退出

    // 文件管理
    Route::any('files', "FilesController@index")->name('files'); // 文件列表
    Route::any('files.visible', "FilesController@visible")->name('files.visible'); // 设置文件可见
    Route::any('files.invisible', "FilesController@invisible")->name('files.invisible'); // 设置文件不可见
    Route::any('files.delete', "FilesController@delete")->name('files.delete'); // 删除文件

    // 数据库表操作日志
    Route::any('logs.tables_operated', "Logs\TablesOperatedController@index")->name('logs.tables_operated'); // 数据库表操作日志列表
    Route::any('logs.tables_operated.info', "Logs\TablesOperatedController@info")->name('logs.tables_operated.info'); // 数据库表操作日志列表
    // 管理员日志
    Route::any('logs.login.admins', "Logs\LoginController@admins")->name('logs.login.admins'); // 登录日志列表
    Route::any('logs.behavior.admins', "Logs\BehaviorController@admins")->name('logs.behavior.admins'); // 行为日志列表
    // 用户日志
    Route::any('logs.login.users', "Logs\LoginController@users")->name('logs.login.users'); // 登录日志列表
    Route::any('logs.behavior.users', "Logs\BehaviorController@users")->name('logs.behavior.users'); // 行为日志列表

    // 管理员账号管理
    Route::any('admins', "Admins\AdminsController@index")->name('admins'); // 账号列表
    Route::any('admins.add', "Admins\AdminsController@add")->name('admins.add'); // 添加账号
    Route::any('admins.update', "Admins\AdminsController@update")->name('admins.update'); // 修改账号
    Route::any('admins.delete', "Admins\AdminsController@delete")->name('admins.delete'); // 删除账号
    Route::any('admins.restore', "Admins\AdminsController@restore")->name('admins.restore'); // 恢复账号
    Route::any('admins.export', "Admins\AdminsController@export")->name('admins.export'); // 账号导出

    // 权限设置
    Route::any('permissions', "Admins\PermissionsController@index")->name('permissions'); // 权限列表
    Route::any('permissions.action', "Admins\PermissionsController@getRouteActionByRouteName")->name('permissions.action'); // 通过route别名查找route方法
    Route::any('permissions.add', "Admins\PermissionsController@add")->name('permissions.add'); // 添加权限
    Route::any('permissions.update', "Admins\PermissionsController@update")->name('permissions.update'); // 修改权限
    Route::any('permissions.delete', "Admins\PermissionsController@delete")->name('permissions.delete'); // 删除权限
    Route::any('permissions.drop', "Admins\PermissionsController@drop")->name('permissions.drop'); // 权限拖拽排序

    // 角色设置
    Route::any('roles', "Admins\RolesController@index")->name('roles'); // 角色列表
    Route::any('roles.add', "Admins\RolesController@add")->name('roles.add'); // 添加角色
    Route::any('roles.update', "Admins\RolesController@update")->name('roles.update'); // 修改角色
    Route::any('roles.delete', "Admins\RolesController@delete")->name('roles.delete'); // 删除角色

    // 用户账号管理
    Route::any('users', "Users\UsersController@index")->name('users'); // 账号列表
    Route::any('users.add', "Users\UsersController@add")->name('users.add'); // 添加账号
    Route::any('users.update', "Users\UsersController@update")->name('users.update'); // 修改账号
    Route::any('users.delete', "Users\UsersController@delete")->name('users.delete'); // 删除账号
    Route::any('users.restore', "Users\UsersController@restore")->name('users.restore'); // 恢复账号
    Route::any('users.export', "Users\UsersController@export")->name('users.export'); // 账号导出


    // 产品品牌管理
    Route::any('brands', "Products\BrandsController@index")->name('brands'); // 品牌列表
    Route::any('brands.add', "Products\BrandsController@add")->name('brands.add'); // 添加品牌
    Route::any('brands.update', "Products\BrandsController@update")->name('brands.update'); // 修改品牌

    // 产品分类管理
    Route::any('category', "Products\CategoryController@index")->name('category'); // 分类列表
    Route::any('category.add', "Products\CategoryController@add")->name('category.add'); // 添加分类
    Route::any('category.update', "Products\CategoryController@update")->name('category.update'); // 修改分类
    Route::any('category.all', "Products\CategoryController@all")->name('category.all'); // 获取所有分类

    // 产品属性分组管理
    Route::any('attributes.group', "Products\AttributesGroupController@index")->name('attributes.group'); // 属性分组列表
    Route::any('attributes.group.add', "Products\AttributesGroupController@add")->name('attributes.group.add'); // 添加属性分组
    Route::any('attributes.group.update', "Products\AttributesGroupController@update")->name('attributes.group.update'); // 修改属性分组

    // 产品属性管理
    Route::any('attributes', "Products\AttributesController@index")->name('attributes'); // 属性列表
    Route::any('attributes.add', "Products\AttributesController@add")->name('attributes.add'); // 添加属性
    Route::any('attributes.update', "Products\AttributesController@update")->name('attributes.update'); // 修改属性

    // 产品规格管理
    Route::any('specs', "Products\SpecsController@index")->name('specs'); // 规格列表
    Route::any('specs.add', "Products\SpecsController@add")->name('specs.add'); // 添加规格
    Route::any('specs.update', "Products\SpecsController@update")->name('specs.update'); // 修改规格

    // 产品服务管理
    Route::any('services', "Products\ServicesController@index")->name('services'); // 服务列表
    Route::any('services.add', "Products\ServicesController@add")->name('services.add'); // 添加服务
    Route::any('services.update', "Products\ServicesController@update")->name('services.update'); // 修改服务


    // 产品信息管理
    Route::any('spu', "Products\SpuController@index")->name('spu'); // 信息列表
    Route::any('spu.add', "Products\SpuController@add")->name('spu.add'); // 添加信息
    Route::any('spu.update', "Products\SpuController@update")->name('spu.update'); // 修改信息

    // 产品库存管理
    Route::any('sku', "Products\SkuController@index")->name('sku'); // 库存列表
    Route::any('sku.update', "Products\SkuController@update")->name('sku.update'); // 修改库存信息
    Route::any('sku.in', "Products\SkuController@in")->name('sku.in'); // 产品采购
    Route::any('sku.out', "Products\SkuController@out")->name('sku.out'); // 产品退货

    // 产品库存进销管理
    Route::any('stock', "Products\StockController@index")->name('stock'); // 库存进销列表

});
