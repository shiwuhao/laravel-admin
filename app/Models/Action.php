<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

/**
 *
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
