<?php

namespace App\Http\Resources;

use App\Policies\UserPolicy;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

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
            'source'      => route('episode.video', $this->id),
            'position'    => $this->position,
            'duration'    => $this->duration,
            'can_see'     => UserPolicy::canViewEpisode(auth()->user(), $this->id),
            'views_count' => $this->views_count,
            'price'       => $this->price ?? 0,
        ];
    }
}
