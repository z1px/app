<?php

use Illuminate\Database\Seeder;
use Z1px\App\Models\ConfigModel;

class ConfigTableSeeder extends Seeder
{

    private $config_model = ConfigModel::class;

    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        request()->offsetSet('command',  "console: php artisan {$this->command->getName()}");
        # 添加默认配置
        array_map(function ($data){
            app($this->config_model)->create($data);
        }, [
            [
                'title' => '小程序 appId',
                'key' => 'weapp_appid',
                'value' => '',
                'brief' => '小程序配置',
                'input' => 'text',
                'values' => [],
                'type' => 21,
                'status' => 1
            ],
            [
                'title' => '小程序 appSecret',
                'key' => 'weapp_secret',
                'value' => '',
                'brief' => '小程序配置',
                'input' => 'text',
                'values' => [],
                'type' => 2,
                'status' => 1
            ],
            [
                'title' => '产品单位选择',
                'key' => 'spu_unit',
                'value' => '',
                'brief' => '产品配置',
                'input' => 'select',
                'values' => ["件", "袋", "台", "个", "箱", "斤", "千克"],
                'type' => 3,
                'status' => 1
            ],
        ]);
    }
}
