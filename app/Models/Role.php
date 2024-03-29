<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Role extends Model
{
    use HasFactory;

    protected $guarded = [];
    const MODERATOR_ID = 3;
    const ADMIN_ID = 1;
    const USER_ID = 2;

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function episodes(): BelongsTo
    {
        return $this->belongsTo(Episode::class);
    }

}
