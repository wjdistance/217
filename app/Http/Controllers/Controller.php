<?php

namespace App\Http\Controllers;

use App\Models\Options;
use App\Models\User;
use App\Models\UserPoints;
use http\Env\Request;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Illuminate\Foundation\Bus\DispatchesJobs;
use Illuminate\Foundation\Validation\ValidatesRequests;
use Illuminate\Routing\Controller as BaseController;
use Cache;

class Controller extends BaseController
{
    use AuthorizesRequests, DispatchesJobs, ValidatesRequests;

    //注册
    public function register(Request $request)
    {

        $data = $request->all();

        $referee_id = $data['referee_id'] ?? 0;
        //有推荐人
        if ($referee_id) {
            $option = Options::pluck('points', 'type')->toArray();

            $this->points($referee_id, $option);
        }
        User::create(['name' => $data['name'], 'referee_id' => $referee_id, 'email'=>$data['email'], 'password' => bcrypt($data['password'])]);
        return true;
    }

    //推荐人获得分数，推荐人上级获得分数
    public function points($referee_id, $option, $number = 0)
    {

        //推荐人上一级
        if ($number != 0) {
            $array = [];
            foreach ($referee_id as $v) {
                //推荐人
                $user = User::where('id', $v)->first();
                if (!$user) {
                    continue;
                }
                $array[] = $user['referee_id'];

                $referee = UserPoints::create(['user_id' => $v, 'points' => $option[1]]);
                $this->points($array, $option, 1);
            }

        } else {
            //推荐人
            $user = User::where('id', $referee_id)->first();
            if (!$user) {
                return false;
            }
            $referee = UserPoints::create(['user_id' => $referee_id, 'points' => $option[0]]);

        }

    }

    //无限极分类
    public function UserPointsSort($user_id)
    {
        if (!Cache::has('users')) {
            $users = User::get(['id', 'name', 'referee_id'])->toArray();
            Cache::add('users', json_encode($users), 60 * 60);
        }
        $array = json_decode(Cache::get('users'), true);

        $array = $this->filterArray($array, $user_id);
        $list = $this->listTree($array, $user_id);

        dd($list);

    }

    //遍历数组
    public function listTree($list, $root = 0)
    {
        $tree = [];
        if (is_array($list)) {
            $array = [];
            foreach ($list as $key => $data) {
                $array[$data['id']] = &$list[$key];
            }
            foreach ($list as $key => $value) {
                $refereeId = $value['referee_id'];

                if ($root == $refereeId) {
                    $tree[] = &$list[$key];
                } else {
                    if (isset($array[$refereeId])) {
                        $parent =& $array[$refereeId];
                        $parent['son'][] =& $list[$key];
                    }
                }
            }
        }
        return $tree;
    }

    //过滤
    public function filterArray($array, $user_id = 0)
    {
        static $arrList = [];
        if (empty($array)) return [];
        foreach ($array as $key => $value) {
            if ($value['referee_id'] == $user_id) {
                $arrList[] = $value;
                unset($array[$key]);
                $this->filterArray($array, $value['id']);
            }
        }
        return $arrList;
    }


}
