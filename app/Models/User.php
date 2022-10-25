<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;
use Shiwuhao\Rbac\Models\Traits\UserTrait;

class User extends Authenticatable
{
    use HasApiTokens, HasFactory, Notifiable, UserTrait, SoftDeletes;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'email',
        'username',
        'nickname',
        'realname',
        'mobile',
        'password',
        'status',
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var array<int, string>
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    protected $appends = [
        'status_label',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'email_verified_at' => 'datetime',
        'created_at' => 'datetime:Y-m-d H:i',
    ];

    public function isDisabled(): bool
    {
        return $this->status == 0;
    }

    /**
     * 超级管理员
     * @return bool
     */
    public function isAdministrator(): bool
    {
        return $this->hasRole(Role::ADMINISTRATOR);
    }

    /**
     * status_label
     * @return Attribute
     */
    public function statusLabel(): Attribute
    {
        return new Attribute(
            get: fn() => $this->status ? '正常' : '禁用',
        );
    }

    /**
     * role_ids
     * @return Attribute
     */
    public function roleIds(): Attribute
    {
        return new Attribute(
            get: fn() => $this->roles->pluck('id')->toArray(),
        );
    }

    /**
     * search
     * @param Builder $builder
     * @param $params
     * @return Builder
     */
    public function scopeOfSearch(Builder $builder, $params): Builder
    {
        if (!empty($params['id'])) {
            $builder->where('id', "{$params['id']}");
        }

        if (!empty($params['username'])) {
            $builder->where('username', 'like', "{$params['username']}%");
        }

        if (!empty($params['nickname'])) {
            $builder->where('nickname', 'like', "{$params['nickname']}%");
        }
        return $builder;
    }
}
