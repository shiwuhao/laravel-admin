<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\HasMany;

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
