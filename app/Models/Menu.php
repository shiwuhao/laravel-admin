<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Shiwuhao\Rbac\Models\Traits\PermissibleTrait;

class Menu extends Model
{
    use HasFactory, SoftDeletes, PermissibleTrait;

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
        self::TYPE_ROUTE => '路由',
        self::TYPE_LINK => '外链',
        self::TYPE_IFRAME => '内嵌',
    ];

    /**
     * @var string[]
     */
    protected $fillable = [
        'pid', 'name', 'label', 'url', 'type', 'icon', 'sort'
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
        'type_label', 'alias', 'meta',
    ];

    /**
     * @var string[]
     */
    protected $casts = [
        'created_at' => 'datetime:Y-m-d H:i',
    ];

    /**
     * children
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function children(): \Illuminate\Database\Eloquent\Relations\HasMany
    {
        return $this->hasMany(Menu::class, 'pid', 'id');
    }

    /**
     * alias
     * @return string
     */
    public function getAliasAttribute(): string
    {
        return $this->name;
    }

    /**
     * type_label
     * @return string
     */
    public function getTypeLabelAttribute(): string
    {
        return self::TYPE_LABEL[$this->type] ?? '';
    }

    /**
     * meta
     * @return \string[][]
     */
    public function getMetaAttribute(): array
    {
        return ['meta' => [
            'title' => $this->label,
            'icon' => $this->icon,
            'sort' => $this->sort,
        ]];
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
