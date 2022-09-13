<?php

namespace Database\Seeders;

use App\Models\Options;
use App\Models\User;
use App\Models\UserPoints;
use Illuminate\Database\Seeder;

class TestSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        //配置
        $options = [
            ['type' => 0, 'points' => 10],
            ['type' => 1, 'points' => 5],
        ];
        foreach ($options as $v) {
            Options::updateOrCreate(['type' => $v['type'], 'points' => $v['points']]);
        }

        //创建用户
        $users = [
            ['name' => '张三' , 'email' => '1231@qq.com', 'referee_id' => 0],
            ['name' => '李四' , 'email' => '1232@qq.com', 'referee_id' => 1],
            ['name' => '王五' , 'email' => '1233@qq.com', 'referee_id' => 2],
        ];
        foreach ($users as $v) {
            User::updateOrCreate(['name' => $v['name'], 'email' => $v['email'], 'referee_id' => $v['referee_id'],  'password' => bcrypt('123456')]);
        }

        //获得积分
        $users = [
            ['user_id' => 1, 'points' => 1],
            ['user_id' => 1, 'points' => 2],
            ['user_id' => 2, 'points' => 3],
            ['user_id' => 2, 'points' => 4],
            ['user_id' => 3, 'points' => 5],
            ['user_id' => 2, 'points' => 6],
            ['user_id' => 3, 'points' => 7],
        ];
        foreach ($users as $v) {
            UserPoints::updateOrCreate(['user_id' => $v['user_id'], 'points' => $v['points']]);
        }


    }
}
