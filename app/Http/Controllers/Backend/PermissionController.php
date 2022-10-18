<?php

namespace App\Http\Controllers\Backend;

use App\Http\Controllers\Controller;
use App\Http\Resources\ApiResource;
use App\Models\Permission;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;

/**
 * 权限节点
 */
class PermissionController extends Controller
{
    /**
     * 排序step
     * @var int
     */
    protected $sortStep = 65536;

    /**
     * @param Request $request
     * @return AnonymousResourceCollection
     */
    public function index(Request $request): AnonymousResourceCollection
    {
        $permissions = Permission::ofSearch($request->all())->with('permissible')->oldest('sort')->oldest('id')->paginate();

        return ApiResource::collection($permissions);
    }

    /**
     * @param Request $request
     * @param Permission $permission
     * @return ApiResource
     */
    public function update(Request $request, Permission $permission): ApiResource
    {
        $dropPermission = Permission::find($request->get('drop_id'));

        $permission = $this->getPermissionByDropType($permission, $dropPermission, $request);

        if ($permission->sort == 0 || !is_int($permission->sort)) { //
            $this->initPermissionSort();
            $permission = $this->getPermissionByDropType($permission, $dropPermission, $request);
        }

        $permission->save();

        return ApiResource::make(['message' => 'success']);
    }

    /**
     * 根据拖拽后节点的位置机选对应的排序值
     * drop_type值为before、after、inner
     * @param Permission $draggingPermission
     * @param Permission $dropPermission
     * @param Request $request
     * @return Permission
     */
    protected function getPermissionByDropType(Permission $draggingPermission, Permission $dropPermission, Request $request): Permission
    {
        switch ($request->get('drop_type')) {
            case 'inner':
                $draggingPermission = $this->getPermissionByInner($draggingPermission, $dropPermission);
                break;
            case 'before':
                $draggingPermission = $this->getPermissionByBefore($draggingPermission, $dropPermission);
                break;
            case 'after':
                $draggingPermission = $this->getPermissionByAfter($draggingPermission, $dropPermission);
                break;
        }

        return $draggingPermission;
    }

    /**
     * 基于前一个对象计算父级ID，排序标识
     * @param Permission $draggingPermission
     * @param Permission $dropPermission
     * @return Permission
     */
    protected function getPermissionByInner(Permission $draggingPermission, Permission $dropPermission): Permission
    {
        $draggingPermission->pid = $dropPermission->id;

        return $draggingPermission;
    }

    /**
     * 基于前一个对象计算父级ID，排序标识
     * @param Permission $draggingPermission
     * @param Permission $dropPermission
     * @return Permission
     */
    protected function getPermissionByBefore(Permission $draggingPermission, Permission $dropPermission): Permission
    {
        $beforePermission = Permission::wherePid($dropPermission->pid)->where('sort', '<', $dropPermission->sort)->latest('sort')->first(); // 5
        if ($beforePermission) { // 放在在两个之间
            $draggingPermission->sort = ($dropPermission->sort + $beforePermission->sort) / 2;
            $draggingPermission->pid = $beforePermission->pid;
        } else { // 放在第一个元素
            $draggingPermission->sort = $dropPermission->sort / 2;
            $draggingPermission->pid = $dropPermission->pid;
        }

        return $draggingPermission;
    }

    /**
     * 基于后一个对象计算父级ID，排序标识
     * @param Permission $draggingPermission
     * @param Permission $dropPermission
     * @return Permission
     */
    protected function getPermissionByAfter(Permission $draggingPermission, Permission $dropPermission): Permission
    {
        $afterPermission = Permission::wherePid($dropPermission->pid)->where('sort', '>', $dropPermission->sort)->oldest('sort')->first();
        if ($afterPermission) { // 放在在两个之间
            $draggingPermission->sort = ($dropPermission->sort + $afterPermission->sort) / 2;
            $draggingPermission->pid = $afterPermission->pid;
        } else { // 放在最后个元素
            $draggingPermission->sort = $dropPermission->sort + $this->sortStep;
            $draggingPermission->pid = $dropPermission->pid;
        }

        return $draggingPermission;
    }

    /**
     * 初始化排序值
     * @return Permission[]|Collection|\Illuminate\Support\Collection
     */
    protected function initPermissionSort()
    {
        return Permission::oldest('sort')->oldest('id')->get()->each(function ($item, $key) {
            $item->sort = ($key + 1) * $this->sortStep;
            $item->save();
        });

    }
}
