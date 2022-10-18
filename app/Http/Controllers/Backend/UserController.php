<?php

namespace App\Http\Controllers\Backend;

use App\Http\Controllers\Controller;
use App\Http\Resources\ApiResource;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;

class UserController extends Controller
{

    /**
     * @param Request $request
     * @return AnonymousResourceCollection
     */
    public function index(Request $request)
    {
        $users = User::ofSearch($request->all())->with('roles')->latest('id')->paginate();

        return ApiResource::collection($users);
    }

    /**
     * @param Request $request
     * @return ApiResource
     */
    public function store(Request $request): ApiResource
    {
        $user = new User($request->all());
        $user->password = bcrypt($request->password);
        $user->save();

        $this->syncRoles($user, $request);

        return ApiResource::make($user);
    }

    /**
     * @param User $user
     * @return ApiResource
     */
    public function show(User $user): ApiResource
    {
        $user->append('role_ids');
        return ApiResource::make($user);
    }

    /**
     * @param Request $request
     * @param User $user
     * @return ApiResource
     */
    public function update(Request $request, User $user): ApiResource
    {
        $user->fill($request->all());
        if ($request->password) {
            $user->password = bcrypt($request->password);
        }
        $user->save();

        $this->syncRoles($user, $request);

        return ApiResource::make($user);
    }

    /**
     * @param $id
     * @return ApiResource
     */
    public function destroy($id): ApiResource
    {
        $user = User::withTrashed()->find($id);
        if (!$user->trashed()) {
            $user->delete();
        }

        return ApiResource::make($user);
    }

    /**
     * 同步角色
     * @param User $user
     * @param Request $request
     */
    protected function syncRoles(User $user, Request $request)
    {
        if ($request->role_ids) {
            $user->roles()->sync($request->role_ids);
            $user->roles;
        }
    }
}
