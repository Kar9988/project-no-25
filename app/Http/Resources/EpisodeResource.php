<?php

namespace App\Http\Resources;

use App\Policies\UserPolicy;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;
use Illuminate\Support\Facades\Storage;

class EpisodeResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return [
            'id'          => $this->id,
            'title'       => $this->title,
            'thumb'       => $this->thumb_path,
            'source'      => $this->source_path,
            'position'    => $this->position,
            'duration'    => $this->duration,
            'can_see'     => UserPolicy::canViewEpisode(auth()->user(), $this->id),
            'views_count' => $this->views_count,
            'likes_count' => $this->likes_count,
            'price'       => $this->price ?? 0,
        ];
    }
}
