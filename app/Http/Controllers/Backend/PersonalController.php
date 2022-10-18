<?php

namespace App\Http\Controllers\Backend;

use App\Http\Controllers\Controller;
use App\Http\Resources\ApiResource;
use App\Models\Action;
use App\Models\Menu;
use App\Models\Permission;
use Illuminate\Http\Request;
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
    public function menus(Request $request): ApiResource
    {
        $user = $request->user();

        $menus = Cache::remember("'userMenus:{$user->id}", 10, function () use ($user) {
            if ($user->isAdministrator()) return Menu::all();

            return $user->permissions()->filter(function ($item) {
                return $item->permissible_type == (new Menu())->getMorphClass();
            });
        });

        return ApiResource::make($menus);
    }
}
