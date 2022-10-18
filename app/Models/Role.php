<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\SoftDeletes;

class Role extends \Shiwuhao\Rbac\Models\Role
{
    use HasFactory;
    use SoftDeletes;

    // 超级管理员标识
    const ADMINISTRATOR = 'administrator';

    /**
     * @var string[]
     */
    protected $fillable = [
        'name', 'title', 'remark', 'status'
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
    protected $appends = [
        'status_label'
    ];

    /**
     * @var string[]
     */
    protected $casts = [
        'created_at' => 'datetime:Y-m-d H:i:s',
    ];

    /**
     * status_label
     * @return Attribute
     */
    protected function statusLabel(): Attribute
    {
        return new Attribute(
            get: fn($value) => $this->status ? '正常' : '禁用',
        );
    }

    /**
     * permission_ids
     * @return Attribute
     */
    protected function permissionIds(): Attribute
    {
        return new Attribute(
            get: fn($value) => $this->permissions->pluck('id')->toArray(),
        );
    }

    /**
     * @param Builder $builder
     * @param array $params
     * @return Builder
     */
    public function scopeOfSearch(Builder $builder, array $params = []): Builder
    {
        if (!empty($params['id'])) {
            $builder->where('id', $params['id']);
        }
        if (!empty($params['title'])) {
            $builder->where('title', 'like', "{$params['title']}%");
        }

        if (!empty($params['name'])) {
            $builder->where('name', 'like', "{$params['name']}%");
        }

        return $builder;
    }
}
