<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class View extends Model
{
    use HasFactory;

    protected $guarded = [];

    public function episode(): BelongsTo
    {
        return $this->belongsTo(Episode::class);
    }
}
