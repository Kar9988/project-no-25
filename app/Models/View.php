<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;

class View extends Model
{
    use HasFactory, SoftDeletes;

    protected $guarded = [];

    public function episode(): BelongsTo
    {
        return $this->belongsTo(Episode::class);
    }
}
