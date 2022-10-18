<?php

namespace App\Http\Controllers\Backend;

use App\Http\Controllers\Controller;
use App\Http\Resources\ApiResource;
use App\Models\Action;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;
use Illuminate\Support\Facades\Artisan;

class ActionController extends Controller
{
    public function all(Request $request)
    {
        $actions = Action::ofSearch($request->all())->get();

        return ApiResource::collection($actions);
    }

    /**
     * @param Request $request
     * @return AnonymousResourceCollection
     */
    public function index(Request $request): AnonymousResourceCollection
    {
        $actions = Action::ofSearch($request->all())->oldest('id')->paginate();

        return ApiResource::collection($actions);
    }

    /**
     * @param Request $request
     * @return ApiResource
     */
    public function store(Request $request): ApiResource
    {
        $action = new Action($request->all());
        $action->save();

        return ApiResource::make($action);
    }

    /**
     * @param Action $action
     * @return ApiResource
     */
    public function show(Action $action): ApiResource
    {
        return ApiResource::make($action);
    }

    /**
     * @param Request $request
     * @param Action $action
     * @return ApiResource
     */
    public function update(Request $request, Action $action): ApiResource
    {
        $action->fill($request->all());
        $action->save();

        return ApiResource::make($action);
    }

    /**
     * @param $id
     * @return ApiResource
     */
    public function destroy($id): ApiResource
    {
        $action = Action::withTrashed()->find($id);
        if (!$action->trashed()) {
            $action->delete();
        }

        return ApiResource::make($action);
    }


    /**
     * 自动生成权限节点
     * @return string
     */
    public function autoGenerate()
    {
        $path = 'backend';
        $exceptPath = ['backend/uploads'];
        Artisan::call('rbac:generate-permissions', [
            '--path' => $path,
            '--except-path' => join(',', $exceptPath)
        ]);

        return 'success';
    }
}
