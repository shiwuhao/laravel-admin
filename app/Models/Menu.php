<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Str;
use Shiwuhao\Rbac\Models\Traits\PermissibleTrait;

/**
 * App\Models\Menu
 *
 * @property int $id
 * @property int $pid 父级ID
 * @property string $name 路由别名
 * @property string $label 显示名称
 * @property string $type 菜单类型
 * @property string $icon 图标
 * @property string $path 路由地址
 * @property string $component 组件
 * @property int $sort 排序
 * @property int $keepalive 是否缓存
 * @property int $affix 是否固定标签栏
 * @property int $hide_menu 隐藏菜单
 * @property \Shiwuhao\Rbac\Models\Permission|null $permission 权限标识
 * @property int $status 状态
 * @property \Illuminate\Support\Carbon|null $deleted_at
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property-read \Illuminate\Database\Eloquent\Collection|Menu[] $children
 * @property-read int|null $children_count
 * @property-read string $alias
 * @property-read string $type_label
 * @method static Builder|Menu newModelQuery()
 * @method static Builder|Menu newQuery()
 * @method static Builder|Menu ofParent()
 * @method static Builder|Menu ofSearch($params)
 * @method static \Illuminate\Database\Query\Builder|Menu onlyTrashed()
 * @method static Builder|Menu query()
 * @method static Builder|Menu whereAffix($value)
 * @method static Builder|Menu whereComponent($value)
 * @method static Builder|Menu whereCreatedAt($value)
 * @method static Builder|Menu whereDeletedAt($value)
 * @method static Builder|Menu whereHideMenu($value)
 * @method static Builder|Menu whereIcon($value)
 * @method static Builder|Menu whereId($value)
 * @method static Builder|Menu whereKeepalive($value)
 * @method static Builder|Menu whereLabel($value)
 * @method static Builder|Menu whereName($value)
 * @method static Builder|Menu wherePath($value)
 * @method static Builder|Menu wherePermission($value)
 * @method static Builder|Menu wherePid($value)
 * @method static Builder|Menu whereSort($value)
 * @method static Builder|Menu whereStatus($value)
 * @method static Builder|Menu whereType($value)
 * @method static Builder|Menu whereUpdatedAt($value)
 * @method static \Illuminate\Database\Query\Builder|Menu withTrashed()
 * @method static \Illuminate\Database\Query\Builder|Menu withoutTrashed()
 * @mixin \Eloquent
 */
class Menu extends Model
{
    use HasFactory, SoftDeletes, PermissibleTrait;

    const TYPE_DIR = 'dir';
    const TYPE_MENU = 'menu';
    const TYPE_GROUP = 'group';

    /**
     * 菜单类型 路由
     */
    const TYPE_ROUTE = 'route';
    /**
     * 菜单类型 外链
     */
    const TYPE_LINK = 'link';
    /**
     * 菜单类型 内嵌
     */
    const TYPE_IFRAME = 'iframe';

    /**
     * type_label
     */
    const TYPE_LABEL = [
        self::TYPE_DIR => '目录',
        self::TYPE_MENU => '菜单',
        self::TYPE_ROUTE => '路由',
        self::TYPE_LINK => '外链',
        self::TYPE_IFRAME => 'iframe',
    ];

    /**
     * @var string[]
     */
    protected $fillable = [
        'pid',
        'name',
        'label',
        'path',
        'component',
        'type',
        'icon',
        'sort',
        'keepalive',
        'affix',
        'hide_menu',
        'status',
    ];

    /**
     * @var string[]
     */
    protected $hidden = [
        'deleted_at', 'updated_at',
    ];

    /**
     * @var string[]
     */
    protected $appends = [
        'type_label', 'alias', 'meta', 'status_label',
    ];

    /**
     * @var string[]
     */
    protected $casts = [
        'created_at' => 'datetime:Y-m-d H:i',
    ];

    /**
     * children
     * @return HasMany
     */
    public function children(): HasMany
    {
        return $this->hasMany(Menu::class, 'pid', 'id');
    }

    /**
     * @return Attribute
     */
    public function alias(): Attribute
    {
        return new Attribute(
            get: $this->name,
        );
    }

    /**
     * name
     * @return Attribute
     */
    public function name(): Attribute
    {
        return new Attribute(
            set: fn($value) => Str::studly($value)
        );
    }

    /**
     * component
     * @return Attribute
     */
    public function component(): Attribute
    {
        return new Attribute(
            set: fn($value) => $this->type == self::TYPE_DIR ? 'LAYOUT' : $value,
        );
    }

    /**
     * type_label
     * @return Attribute
     */
    public function typeLabel(): Attribute
    {
        return new Attribute(
            get: fn() => self::TYPE_LABEL[$this->type] ?? '',
        );
    }

    /**
     * statu_label
     * @return Attribute
     */
    public function statusLabel(): Attribute
    {
        return new Attribute(
            get: fn() => $this->status ? '启用' : '禁用',
        );
    }

    /**
     * @return Attribute
     */
    public function meta(): Attribute
    {
        return new Attribute(
            get: fn() => [
                'title' => $this->label,
                'icon' => $this->icon,
                'sort' => $this->sort,
                'affix' => (bool)$this->affix,
                'keepalive' => (bool)$this->keepalive,
                'hideMenu' => (bool)$this->hide_menu,
                'permission' => (bool)$this->permission,
            ]
        );
    }

    /**
     * @param Builder $builder
     * @return Builder
     */
    public function scopeOfParent(Builder $builder): Builder
    {
        return $builder->where('pid', 0);
    }

    /**
     * @param Builder $builder
     * @param $params
     * @return Builder
     */
    public function scopeOfSearch(Builder $builder, $params): Builder
    {
        if (!empty($params['id'])) {
            $builder->where('id', "{$params['id']}");
        }

        if (!empty($params['name'])) {
            $builder->where('name', 'like', "{$params['name']}%");
        }

        if (!empty($params['label'])) {
            $builder->where('label', 'like', "{$params['label']}%");
        }
        return $builder;
    }
}
