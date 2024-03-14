<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
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

    /**
     * @return string
     */
    public function getThumbPathAttribute(): string
    {
        return Storage::disk('spaces')->temporaryUrl($this->thumb, now()->addMinutes(50));
    }

    /**
     * @return string
     */
    public function getSourcePathAttribute(): string
    {
        return Storage::disk('spaces')->temporaryUrl($this->source, now()->addMinutes(50));
    }
}
