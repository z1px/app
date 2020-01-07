<?php

namespace Z1px\App\Providers;


use Z1px\App\Http\Services\Admins\AdminsPermissionsService;
use Z1px\App\Http\Services\Admins\AdminsRolesService;
use Z1px\App\Http\Services\Admins\AdminsService;
use Z1px\App\Http\Services\Admins\PermissionsService;
use Z1px\App\Http\Services\Admins\RolesPermissionsService;
use Z1px\App\Http\Services\Admins\RolesService;
use Z1px\App\Http\Services\Admins\AdminsBehaviorService;
use Z1px\App\Http\Services\Admins\AdminsLoginService;

use Z1px\App\Http\Services\ConfigService;
use Z1px\App\Http\Services\FilesService;

use Z1px\App\Http\Services\Products\AttributesGroupService;
use Z1px\App\Http\Services\Products\AttributesService;
use Z1px\App\Http\Services\Products\BrandsService;
use Z1px\App\Http\Services\Products\CategoryAttributesService;
use Z1px\App\Http\Services\Products\CategoryService;
use Z1px\App\Http\Services\Products\ServicesService;
use Z1px\App\Http\Services\Products\SkuService;
use Z1px\App\Http\Services\Products\SkuSpecsService;
use Z1px\App\Http\Services\Products\SpecsService;
use Z1px\App\Http\Services\Products\SpuAttributesService;
use Z1px\App\Http\Services\Products\SpuFilesService;
use Z1px\App\Http\Services\Products\SpuService;
use Z1px\App\Http\Services\Products\SpuServicesService;
use Z1px\App\Http\Services\Products\SpuSpecsService;
use Z1px\App\Http\Services\Products\StockService;

use Z1px\App\Http\Services\TablesOperatedService;

use Z1px\App\Http\Services\Users\UsersBehaviorService;
use Z1px\App\Http\Services\Users\UsersLoginService;
use Z1px\App\Http\Services\Users\UsersPassportsService;
use Z1px\App\Http\Services\Users\UsersService;

use Illuminate\Support\ServiceProvider;

use Z1px\App\Http\Logics\Admins\MenuLogic;

class ModelServiceProvider extends ServiceProvider
{
    /**
     * Register services.
     *
     * @return void
     */
    public function register()
    {

        // 逻辑层模型别名
        $this->app->alias(MenuLogic::class, 'menu_logic');

        // 服务层模型别名
        $this->app->alias(ConfigService::class, 'config_service');
        $this->app->alias(TablesOperatedService::class, 'tables_operated_service');
        $this->app->alias(FilesService::class, 'files_service');

        $this->app->alias(AdminsService::class, 'admins_service');
        $this->app->alias(AdminsLoginService::class, 'admins_login_service');
        $this->app->alias(AdminsBehaviorService::class, 'admins_behavior_service');

        $this->app->alias(PermissionsService::class, 'permissions_service');
        $this->app->alias(RolesService::class, 'roles_service');
        $this->app->alias(RolesPermissionsService::class, 'roles_permissions_service');
        $this->app->alias(AdminsRolesService::class, 'admins_roles_service');
        $this->app->alias(AdminsPermissionsService::class, 'admins_permissions_service');

        $this->app->alias(UsersService::class, 'users_service');
        $this->app->alias(UsersPassportsService::class, 'users_passports_service');
        $this->app->alias(UsersLoginService::class, 'users_login_service');
        $this->app->alias(UsersBehaviorService::class, 'users_behavior_service');

        $this->app->alias(BrandsService::class, 'brands_service');
        $this->app->alias(CategoryService::class, 'category_service');
        $this->app->alias(AttributesGroupService::class, 'attributes_group_service');
        $this->app->alias(AttributesService::class, 'attributes_service');
        $this->app->alias(CategoryAttributesService::class, 'category_attributes_service');
        $this->app->alias(SpecsService::class, 'specs_service');
        $this->app->alias(ServicesService::class, 'services_service');
        $this->app->alias(SpuService::class, 'spu_service');
        $this->app->alias(SpuAttributesService::class, 'spu_attributes_service');
        $this->app->alias(SpuSpecsService::class, 'spu_specs_service');
        $this->app->alias(SpuServicesService::class, 'spu_services_service');
        $this->app->alias(SpuFilesService::class, 'spu_files_service');
        $this->app->alias(SkuService::class, 'sku_service');
        $this->app->alias(SkuSpecsService::class, 'sku_specs_service');
        $this->app->alias(StockService::class, 'stock_service');

    }

    /**
     * Bootstrap services.
     *
     * @return void
     */
    public function boot()
    {
        //
    }
}
