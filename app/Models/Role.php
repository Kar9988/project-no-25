<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;

class Role extends Model
{
    use HasFactory, SoftDeletes;

    protected $guarded = [];
    const MODERATOR_ID = 3;
    const ADMIN_ID = 1;
    const USER_ID = 2;

    public function user(): HasMany
    {
        return $this->hasMany(User::class);
    }
}
