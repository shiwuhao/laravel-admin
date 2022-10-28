<?php

namespace App\Http\Controllers\Backend;

use App\Http\Controllers\Controller;
use App\Http\Resources\ApiResource;
use App\Models\Action;
use App\Models\Menu;
use App\Models\Permission;
use Illuminate\Http\Request;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Cache;

/**
 * Class PersonalController
 * @package App\Http\Controllers\Backend
 */
class PersonalController extends Controller
{
    /**
     * 个人信息
     * @param Request $request
     * @return ApiResource
     */
    public function info(Request $request): ApiResource
    {
        $user = $request->user();

        return ApiResource::make($user);
    }

    /**
     * 菜单、权限节点
     * @param Request $request
     * @return ApiResource
     */
    public function permissions(Request $request): ApiResource
    {
        $user = $request->user();

        $permissions = Cache::remember("userPermissions:{$user->id}", 10, function () use ($user) {
            if ($user->isAdministrator()) return Action::all()->pluck('name');
            return $user->permissions()->filter(function ($item) {
                return $item->permissible_type == (new Action())->getMorphClass();
            })->pluck('permissible')->pluck('name');
        });

        return ApiResource::make($permissions);
    }

    /**
     * 菜单
     * @param Request $request
     * @return ApiResource
     */
    public function menus(Request $request)
    {
        $user = $request->user();

        $menus = Cache::remember("'userMenus:{$user->id}", 0, function () use ($user) {
            if ($user->isAdministrator()) {
                $menus = Menu::all();
            } else {
                $menus = $user->permissions()->filter(function ($item) {
                    return $item->permissible_type == app(Menu::class)->getMorphClass();
                })->pluck('permissible');
            }
            $menus = $menus->map(function (Menu $menu) {
                return $menu->only(['id', 'pid', 'sort', 'name', 'path', 'component', 'meta']);
            })->sortByDesc('sort')->values()->toArray();

            return $this->listToTree($menus, $pk = 'id', $pid = 'pid', $child = 'children');
        });


        return ApiResource::make($menus);
    }

    function listToTree($list, $pk = 'id', $pid = 'pid', $child = '_child', $root = 0)
    {
        // 创建Tree
        $tree = array();
        if (is_array($list)) {
            // 创建基于主键的数组引用
            $refer = array();
            foreach ($list as $key => $data) {
                $refer[$data[$pk]] =& $list[$key];
            }
            foreach ($list as $key => $data) {
                // 判断是否存在parent
                $parentId = $data[$pid];
                if ($root == $parentId) {
                    $tree[] =& $list[$key];
                } else {
                    if (isset($refer[$parentId])) {
                        $parent =& $refer[$parentId];
                        $parent[$child][] =& $list[$key];
                    }
                }
            }
        }
        return $tree;
    }
}
