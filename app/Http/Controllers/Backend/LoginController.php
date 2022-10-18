<?php

namespace App\Http\Controllers\Backend;

use App\Exceptions\ApiException;
use App\Http\Resources\ApiResource;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\ValidationException;

/**
 * Class LoginController
 * @package App\Http\Controllers\Api
 */
class LoginController extends BackendController
{
    /**
     * @var string
     */
    protected $tokenName = 'backend';


    public function loginByPassword(Request $request)
    {
        $this->validate($request, [
            'username' => 'required',
            'password' => 'required',
        ]);

        $user = User::where('username', $request->get('username'))->first();
        if (empty($user) || !Hash::check($request->password, $user->password)) {
            throw new ApiException('用户名或密码错误');
        }

        if ($user->isDisabled()) {
            throw new ApiException('账户状态异常，请联系管理员');
        }

        return ApiResource::make($this->grantAccessToken($user));
    }

    public function logout()
    {
        $user = Auth::guard('sanctum')->user();
        if ($user) {
            $user->tokens()->where('name', $this->tokenName)->delete();
        }

        return ApiResource::make([]);
    }

    /**
     * 发放token
     * @param User $user
     * @return array
     */
    protected function grantAccessToken(User $user)
    {
        $name = 'backend';
//        $user->tokens()->where('name', $name)->delete();
        $token = $user->createToken($name);

        return ['access_token' => $token->plainTextToken];
    }
}
