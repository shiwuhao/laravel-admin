<?php

namespace App\Http\Controllers\Backend;

use App\Http\Controllers\Controller;
use App\Http\Resources\ApiResource;
use Illuminate\Http\Request;

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
    public function info(Request $request)
    {
        $user = $request->user();
        $user->roles = ['Administrator'];
        return ApiResource::make($user);
    }

    /**
     * 菜单、权限节点
     * @param Request $request
     * @return ApiResource
     */
    public function permissions(Request $request)
    {
        $user = $request->user();
        $menus = $user->getPermissionMenus(['id', 'pid', 'title', 'icon', 'url', 'type']);
        $actions = $user->getPermissionActions()->pluck('name')->toArray();
        $roles = $user->roles->pluck('name')->toArray();

        return ApiResource::make([
            'menus' => $menus,
            'roles' => $roles,
            'actions' => $actions,
        ]);
    }


}
