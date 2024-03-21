<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\SoftDeletes;

class UserEpisodesHistory extends Model
{
    use HasFactory, SoftDeletes;

    protected $guarded = [];

    public function episode(): BelongsTo
    {
        return $this->BelongsTo(Episode::class);
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
