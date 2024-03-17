<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class LibraryResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return [
            'id'             => $this->id,
            'title'          => $this->title,
            'thumb'          => $this->thumb_path,
            'video_id'       => $this->video_id,
            'source'         => $this->source_path,
            'position'       => $this->position,
            'duration'       => $this->duration??0,
            'views_count'    => $this->views_count,
            'episodes_count' => $this->episodes_count,
            'likes_count'    => $this->likes_count,
            'price'          => $this->price ?? 0,
        ];
    }
}
