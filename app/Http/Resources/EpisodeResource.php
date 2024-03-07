<?php

namespace App\Http\Resources;

use App\Models\Episode;
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
            'thumb'       => $this->thumb,
            'source'      => $this->source,
            'position'    => $this->position,
            'duration'    => $this->duration,
            'views_count' => $this->views_count,
            'price'       => $this->price ?? 0,
        ];
    }
}
