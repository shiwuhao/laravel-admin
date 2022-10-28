<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

/**
 * App\Models\Action
 *
 * @property int $id
 * @property string $name 唯一标识
 * @property string $label 显示名称
 * @property string $method 请求方式
 * @property string $uri 请求路径
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property-read string $alias
 * @property-read \Shiwuhao\Rbac\Models\Permission|null $permission
 * @method static Builder|Action newModelQuery()
 * @method static Builder|Action newQuery()
 * @method static Builder|Action ofSearch($params)
 * @method static Builder|Action query()
 * @method static Builder|Action whereCreatedAt($value)
 * @method static Builder|Action whereId($value)
 * @method static Builder|Action whereLabel($value)
 * @method static Builder|Action whereMethod($value)
 * @method static Builder|Action whereName($value)
 * @method static Builder|Action whereUpdatedAt($value)
 * @method static Builder|Action whereUri($value)
 * @mixin \Eloquent
 */
class Action extends \Shiwuhao\Rbac\Models\Action
{
    use HasFactory;

    /**
     * @var string[]
     */
    protected $fillable = [
        'name', 'label', 'method', 'uri'
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
        'created_at' => 'datetime:Y-m-d H:i',
    ];

    /**
     * @param Builder $builder
     * @param $params
     * @return Builder
     */
    public function scopeOfSearch(Builder $builder, $params): Builder
    {
        if (!empty($params['name'])) {
            $builder->where('name', 'like', "{$params['name']}%");
        }

        if (!empty($params['label'])) {
            $builder->where('label', 'like', "{$params['label']}%");
        }
        return $builder;
    }
}
