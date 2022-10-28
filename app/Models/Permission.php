<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\HasMany;

/**
 * App\Models\Permission
 *
 * @property int $id
 * @property int $pid 父级ID
 * @property string $permissible_type
 * @property int $permissible_id
 * @property int $sort 排序
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property-read \Illuminate\Database\Eloquent\Collection|Permission[] $children
 * @property-read int|null $children_count
 * @property-read \Illuminate\Database\Eloquent\Model|\Eloquent $permissible
 * @property-read \Illuminate\Database\Eloquent\Collection|Permission[] $roles
 * @property-read int|null $roles_count
 * @method static Builder|Permission newModelQuery()
 * @method static Builder|Permission newQuery()
 * @method static Builder|Permission ofParent()
 * @method static Builder|Permission ofSearch(array $params = [])
 * @method static Builder|Permission query()
 * @method static Builder|Permission whereCreatedAt($value)
 * @method static Builder|Permission whereId($value)
 * @method static Builder|Permission wherePermissibleId($value)
 * @method static Builder|Permission wherePermissibleType($value)
 * @method static Builder|Permission wherePid($value)
 * @method static Builder|Permission whereSort($value)
 * @method static Builder|Permission whereUpdatedAt($value)
 * @mixin \Eloquent
 */
class Permission extends \Shiwuhao\Rbac\Models\Permission
{
    use HasFactory;

    const TYPE_MENU = 'menu';
    const TYPE_ACTION = 'action';

    const TYPE_LABEL = [
        self::TYPE_MENU => '菜单',
        self::TYPE_ACTION => '动作',
    ];

    /**
     * @var string[]
     */
    protected $fillable = [
        'pid', 'type', 'name', 'title', 'method', 'url', 'icon',
    ];

    protected $appends = [
        'permissible_type_label'
    ];

    /**
     * @var string[]
     */
    protected $hidden = [
        'updated_at'
    ];

    /**
     * @var string[]
     */
    protected $casts = [
        'created_at' => 'datetime:Y-m-d H:i:s',
    ];

    /**
     * @return HasMany
     */
    public function children(): HasMany
    {
        return $this->hasMany(Permission::class, 'pid', 'id');
    }

    /**
     * type_label
     * @return Attribute
     */
    protected function permissibleTypeLabel(): Attribute
    {
        return new Attribute(
            get: fn() => self::TYPE_LABEL[$this->permissible_type] ?? ''
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
     * @param array $params
     * @return Builder
     */
    public function scopeOfSearch(Builder $builder, array $params = []): Builder
    {
        if (!empty($params['name'])) {
            $builder->where('name', 'like', "{$params['name']}%");
        }
        if (!empty($params['url'])) {
            $builder->where('url', 'like', "{$params['url']}%");
        }
        if (!empty($params['id'])) {
            $builder->where('id', $params['id']);
        }

        if (!empty($params['pid'])) {
            $builder->where('pid', $params['pid']);
        }

        return $builder;
    }

    public function toJson($options = 256)
    {
        return parent::toJson($options);
    }
}
