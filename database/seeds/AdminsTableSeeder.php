<?php

use Illuminate\Database\Seeder;

class AdminsTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        request()->offsetSet('command',  "console: php artisan {$this->command->getName()}");
        # 添加默认管理员账号
        $check = app('admins_service')
            ->where("username", "sky001")
            ->count();
        if(0 === $check){
            app('admins_service')->reguard();
            app('admins_service')->fill([
                'username' => 'sky001',
                'password' => 'sky123'
            ])->save();
        }
    }
}
