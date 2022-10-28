<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\SoftDeletes;

/**
 * App\Models\Role
 *
 * @property int $id
 * @property string $name 唯一标识
 * @property string $label
 * @property string $desc
 * @property int $status 状态
 * @property \Illuminate\Support\Carbon|null $deleted_at
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property-read \Illuminate\Database\Eloquent\Collection|\App\Models\Permission[] $permissions
 * @property-read int|null $permissions_count
 * @property-read \Illuminate\Database\Eloquent\Collection|\App\Models\User[] $users
 * @property-read int|null $users_count
 * @method static Builder|Role newModelQuery()
 * @method static Builder|Role newQuery()
 * @method static Builder|Role ofSearch(array $params = [])
 * @method static \Illuminate\Database\Query\Builder|Role onlyTrashed()
 * @method static Builder|Role query()
 * @method static Builder|Role whereCreatedAt($value)
 * @method static Builder|Role whereDeletedAt($value)
 * @method static Builder|Role whereDesc($value)
 * @method static Builder|Role whereId($value)
 * @method static Builder|Role whereLabel($value)
 * @method static Builder|Role whereName($value)
 * @method static Builder|Role whereStatus($value)
 * @method static Builder|Role whereUpdatedAt($value)
 * @method static \Illuminate\Database\Query\Builder|Role withTrashed()
 * @method static \Illuminate\Database\Query\Builder|Role withoutTrashed()
 * @mixin \Eloquent
 */
class Role extends \Shiwuhao\Rbac\Models\Role
{
    use HasFactory;
    use SoftDeletes;

    // 超级管理员标识
    const ADMINISTRATOR = 'Administrator';

    /**
     * @var string[]
     */
    protected $fillable = [
        'name', 'title', 'remark', 'status'
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
        'updated_at' => 'datetime:Y-m-d H:i:s',
    ];

    /**
     * status_label
     * @return Attribute
     */
    protected function statusLabel(): Attribute
    {
        return new Attribute(
            get: fn($value) => $this->status ? '启用' : '禁用',
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
