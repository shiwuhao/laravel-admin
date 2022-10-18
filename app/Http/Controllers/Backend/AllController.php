<?php

namespace App\Http\Controllers\Backend;

use App\Http\Controllers\Controller;
use App\Http\Resources\ApiResource;
use App\Models\Menu;
use App\Models\Permission;
use App\Models\Role;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;

class AllController extends Controller
{
    /**
     * @param Request $request
     * @return AnonymousResourceCollection
     */
    public function menus(Request $request): AnonymousResourceCollection
    {
        $menus = Menu::ofSearch($request->all())->oldest('id')->get();

        return ApiResource::collection($menus);
    }

    /**
     * @param Request $request
     * @return AnonymousResourceCollection
     */
    public function permissions(Request $request): AnonymousResourceCollection
    {
        $permissions = Permission::ofSearch($request->all())->with('permissible')->oldest('sort')->oldest('id')->get();

        return ApiResource::collection($permissions);
    }

    /**
     * @param Request $request
     * @return AnonymousResourceCollection
     */
    public function roles(Request $request): AnonymousResourceCollection
    {
        $roles = Role::ofSearch($request->all())->oldest('id')->paginate();

        return ApiResource::collection($roles);
    }

}
