<?php
/**
 * Created by PhpStorm.
 * User: kevin
 * Date: 2019/10/24
 * Time: 8:51 上午
 */


namespace Z1px\App\Http\Logics\Admins;


class MenuLogic
{

    /**
     * 菜单列表
     * @return \Illuminate\Config\Repository|mixed
     */
    public function toList()
    {
        $list_menu = config("admin.list_menu", []);

        $cache_name = 'cache_list_menu';

        // 配置展示的菜单栏
        $url = request()->path();
        $cache = app('cache')->get($cache_name);
        if(empty($cache) || !isset($cache[$url])){
            $func = function ($data) use ($url){
                $result = false;
                if(is_array($data) && !empty($data)){
                    foreach ($data as $key=>$value){
                        if(isset($value['url']) && ($url === $value['url'] || strpos($url, $value['url'] . '.') === 0)){
                            $result = true;
                            break;
                        }
                    }
                }
                return $result;
            };
            foreach ($list_menu as $key=>$value){
                if(isset($value['list']) && $func($value['list'])){
                    $cache[$url] = $key;
                    break;
                }
            }
            app('cache')->put($cache_name, $cache);
        }
        unset($cache_name);

        if(isset($cache[$url]) && isset($list_menu[$cache[$url]])){
            $list_menu[$cache[$url]]['active'] = 'active';
            foreach ($list_menu[$cache[$url]]['list'] as $k=>$val){
                if(isset($val['url']) && ($url === $val['url'] || strpos($url, $val['url'] . '.') === 0)){
                    $list_menu[$cache[$url]]['list'][$k]['active'] = 'active';
                    break;
                }
            }
        }

        return $list_menu;
    }
}
