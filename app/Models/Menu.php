<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Shiwuhao\Rbac\Models\Traits\PermissibleTrait;

class Menu extends Model
{
    use HasFactory, SoftDeletes, PermissibleTrait;
}
