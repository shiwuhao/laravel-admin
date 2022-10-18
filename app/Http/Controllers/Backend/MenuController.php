<?php

namespace App\Http\Controllers\Backend;

use App\Http\Controllers\Controller;
use App\Http\Resources\ApiResource;
use App\Models\Menu;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;

class MenuController extends Controller
{
    /**
     * @param Request $request
     * @return AnonymousResourceCollection
     */
    public function index(Request $request): AnonymousResourceCollection
    {
        $menus = Menu::ofSearch($request->all())->ofParent()->oldest('id')->with('children')->paginate();

        return ApiResource::collection($menus);
    }

    /**
     * @param Request $request
     * @return ApiResource
     */
    public function store(Request $request): ApiResource
    {
        $menu = new Menu($request->all());
        $menu->save();

        return ApiResource::make($menu);
    }

    /**
     * @param Menu $menu
     * @return ApiResource
     */
    public function show(Menu $menu): ApiResource
    {
        return ApiResource::make($menu);
    }

    /**
     * @param Request $request
     * @param Menu $menu
     * @return ApiResource
     */
    public function update(Request $request, Menu $menu): ApiResource
    {
        $menu->fill($request->all());
        $menu->save();

        return ApiResource::make($menu);
    }

    /**
     * @param $id
     * @return ApiResource
     */
    public function destroy($id): ApiResource
    {
        $menu = Menu::withTrashed()->find($id);
        if (!$menu->trashed()) {
            $menu->delete();
        }

        return ApiResource::make($menu);
    }
}
