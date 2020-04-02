Laravel6通用后台解决方案
===============

# 安装
> composer require z1px/tool:dev-master
> composer require z1px/app:dev-master

# 使用
1. 拷贝database里面的数据库迁移文件，到laravel框架的database目录对应的文件夹
2. 在laravel项目的根目录下，执行
    > php artisan migrate --seed
    
    进行数据库迁移和数据填充
3. 修改laravel项目的RouteServiceProvider.php文件，进行多模块配置，配置可参考本项目的src\Providers\RouteServiceProvider.php文件
4. 中间间修改，在laravel项目的Kernel.php文件中的中间件分组middlewareGroups中，增加
    > \Z1px\App\Http\Middleware\Admin\BeforeMiddleware::class,
                                                               
    和
    > \Z1px\App\Http\Middleware\Admin\AfterMiddleware::class,
                                                                                                                                                                                                  
    在中间件别名routeMiddleware中，增加
    > 'admin.auth' => \Z1px\App\Http\Middleware\Admin\AuthMiddleware::class,
    
    该方法是后台系统的登录与权限判断中间件
    
5. 其它方法，如助手函数helpers.php，异常捕捉Handler.php，数据库创建命令CreateDatabase.php，路由方法等根据自己的需求添加
6. 基本的增删改查，登录注销已经实现好了，可以直接调用

## 注意
> 该项目已经实现了增删改查的Trait方法，可直接使用，大大增加了后台的开发速度，前提是模型必须要继承 \Z1px\App\Models\Model.php 类

## 该项目已实现的数据库迁移表包括：
* 通用配置
* 数据库表操作日志
* 文件资源管理表
* 后台管理员账号
* 角色
* 权限
* 后台管理员登录日志
* 后台管理员行为日志

## 该项目已实现的功能包括以下：
* 以上表的增删改查方法
* 登录/登录日志记录
* 中间件验证登录与权限，让开发只需关注业务的实现
* 中间件实现行为日志自动记录
* 助手函数实现返回和跳转方法的统一处理
* 增加自动创建数据库命令
* 异常处理
* 多模块多域名配置
* 增删改查的Trait方法实现
* Model类增加只读字段配置和数据库表增删改自动记录

## 完整的项目参考
[laravel6 + vue2.0 通用后台管理系统](https://github.com/z1px/demo)
