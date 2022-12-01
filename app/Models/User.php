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

/**
 * App\Models\User
 *
 * @property int $id
 * @property string $email
 * @property string $realname
 * @property string $nickname
 * @property string $mobile
 * @property string $avatar 头像
 * @property int $gender 性别
 * @property int $status 状态
 * @property string $source 来源
 * @property \Illuminate\Support\Carbon|null $email_verified_at
 * @property string $password
 * @property string|null $remember_token
 * @property \Illuminate\Support\Carbon|null $deleted_at
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property-read \Illuminate\Notifications\DatabaseNotificationCollection|\Illuminate\Notifications\DatabaseNotification[] $notifications
 * @property-read int|null $notifications_count
 * @property-read \Illuminate\Database\Eloquent\Collection|\App\Models\Role[] $roles
 * @property-read int|null $roles_count
 * @property-read \Illuminate\Database\Eloquent\Collection|\Laravel\Sanctum\PersonalAccessToken[] $tokens
 * @property-read int|null $tokens_count
 * @method static \Database\Factories\UserFactory factory(...$parameters)
 * @method static Builder|User newModelQuery()
 * @method static Builder|User newQuery()
 * @method static Builder|User ofSearch($params)
 * @method static \Illuminate\Database\Query\Builder|User onlyTrashed()
 * @method static Builder|User query()
 * @method static Builder|User whereAvatar($value)
 * @method static Builder|User whereCreatedAt($value)
 * @method static Builder|User whereDeletedAt($value)
 * @method static Builder|User whereEmail($value)
 * @method static Builder|User whereEmailVerifiedAt($value)
 * @method static Builder|User whereGender($value)
 * @method static Builder|User whereId($value)
 * @method static Builder|User whereMobile($value)
 * @method static Builder|User whereNickname($value)
 * @method static Builder|User wherePassword($value)
 * @method static Builder|User whereRealname($value)
 * @method static Builder|User whereRememberToken($value)
 * @method static Builder|User whereSource($value)
 * @method static Builder|User whereStatus($value)
 * @method static Builder|User whereUpdatedAt($value)
 * @method static \Illuminate\Database\Query\Builder|User withTrashed()
 * @method static \Illuminate\Database\Query\Builder|User withoutTrashed()
 * @mixin \Eloquent
 */
class User extends Authenticatable
{
    use HasApiTokens, HasFactory, Notifiable, UserTrait, SoftDeletes;

    protected $perPage = 15;

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
