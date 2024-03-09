<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

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
     * @return string
     */
    public function getThumbPathAttribute(): string
    {
        return asset("storage/$this->thumb");
    }
}
