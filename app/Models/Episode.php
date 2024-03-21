<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\MorphMany;
use Illuminate\Support\Facades\Storage;

class Episode extends Model
{
    use HasFactory;

    protected $guarded = [];

    /**
     * @return void
     */
    protected static function boot(): void
    {
        parent::boot();

        static::addGlobalScope('order', function (Builder $builder) {
            $builder->orderBy('position', 'asc');
        });
    }

    /**
     * @return HasMany
     */
    public function episodes(): HasMany
    {
        return $this->hasMany(self::class, 'video_id', 'video_id');
    }
    public function history(): BelongsToMany
    {
        return $this->belongsToMany(User_episodes_history::class);
    }
    /**
     * @return HasMany
     */
    public function views(): HasMany
    {
        return $this->hasMany(View::class);
    }

    /**
     * @return MorphMany
     */
    public function likes(): MorphMany
    {
        return $this->morphMany(Like::class, 'likeable');
    }

    public function isLikedByUser($userId): bool
    {
        return $this->likes()->where('user_id', $userId)->exists();
    }


    /**
     * @return string
     */
    public function getThumbPathAttribute(): string
    {
        return Storage::disk('spaces')->url($this->thumb);
    }

    /**
     * @return string
     */
    public function getSourcePathAttribute(): string
    {
        return Storage::disk('spaces')->url($this->source);
    }
}
