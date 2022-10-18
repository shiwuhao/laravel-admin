<?php

namespace App\Http\Controllers\Backend;

use App\Http\Controllers\Controller;
use App\Http\Resources\ApiResource;
use App\Models\Config;
use App\Models\Menu;
use App\Models\Permission;
use App\Models\Role;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;
use Illuminate\Support\Facades\Cache;

class ConfigController extends Controller
{

    /**
     * @param Request $request
     * @return AnonymousResourceCollection
     */
    public function index(Request $request)
    {
        $configs = Config::ofSearch($request->all())->latest('sort')->latest('id')->paginate();

        return ApiResource::collection($configs);
    }

    /**
     * @param Request $request
     * @return ApiResource
     */
    public function store(Request $request): ApiResource
    {
        $config = new Config($request->all());
        $config->save();

        return ApiResource::make($config);
    }

    /**
     * @param Config $config
     * @return ApiResource
     */
    public function show(Config $config): ApiResource
    {
        return ApiResource::make($config);
    }

    /**
     * @param Request $request
     * @param Config $config
     * @return ApiResource
     */
    public function update(Request $request, Config $config): ApiResource
    {
        $config->fill($request->all());
        $config->save();

        return ApiResource::make($config);
    }

    /**
     * @param $id
     * @return ApiResource
     */
    public function destroy($id): ApiResource
    {
        $config = Config::withTrashed()->find($id);
        if (!$config->trashed()) {
            $config->delete();
        }

        return ApiResource::make($config);
    }

    /**
     * 获取分组下配置数据
     * @param Request $request
     * @return AnonymousResourceCollection
     */
    public function group(Request $request): AnonymousResourceCollection
    {
        $group = $request->get('group') ?? Config::GROUP_BASIC;

        $configs = Config::ofGroup($group)->latest('sort')->latest('id')->get();

        return ApiResource::collection($configs);
    }

    /**
     * 更新分组下配置数据
     * @param Request $request
     * @return ApiResource
     */
    public function groupUpdate(Request $request): ApiResource
    {
        foreach ($request->all() as $name => $value) {
            Config::where('name', $name)->update(['value' => $value]);
        }

        return ApiResource::make([]);
    }

    /**
     * @return ApiResource
     */
    public function configItems(): ApiResource
    {
        $configs = Cache::remember('configs', 100, function () {
            return Config::all()->pluck('parse_value', 'name')
                ->merge($this->getGlobalAppendConfig())->toArray();
        });

        return ApiResource::make($configs);
    }

    /**
     * 需要追加的全局配置项
     * @return array
     */
    protected function getGlobalAppendConfig(): array
    {
        return [
            'config_groups' => $this->toDeepArray(Config::GROUP_LABEL),
            'config_types' => $this->toDeepArray(Config::TYPE_LABEL),
            'config_components' => $this->toDeepArray(Config::COMPONENT_LABEL),
            'menu_types' => $this->toDeepArray(Menu::TYPE_LABEL),
        ];
    }

    /**
     * 一维数组转二维
     * @param array $array
     * @return array
     */
    protected function toDeepArray(array $array = []): array
    {
        $data = [];
        foreach ($array as $key => $value) {
            $data[] = ['value' => $key, 'label' => $value];
        }
        return $data;
    }
}
