<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\MorphMany;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Facades\Storage;
use Staudenmeir\EloquentEagerLimit\HasEagerLimit;

class Video extends Model
{
    use HasFactory, SoftDeletes, HasEagerLimit;

    protected $guarded = [];

    /**
     * @return HasMany
     */
    public function episodes(): HasMany
    {
        return $this->hasMany(Episode::class)->orderBy('position');
    }

    /**
     * @return BelongsTo
     */
    public function category(): BelongsTo
    {
        return $this->belongsTo(Category::class);
    }

    /**
     * @return MorphMany
     */
    public function likes(): MorphMany
    {
        return $this->morphMany(Like::class, 'likeable');
    }

    /**
     * @return string
     */
    public function getCoverImgPathAttribute(): string
    {
        return Storage::disk('spaces')->temporaryUrl($this->cover_img, now()->addMinutes(5));
    }
}
