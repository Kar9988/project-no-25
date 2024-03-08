<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class VideoResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return [
            'id'            => $this->id,
            'description'   => $this->description,
            'title'         => $this->title,
            'cover_img'     => $this->cover_img_path,
            'category_name' => $this->category?->name,
            'episodes'      => EpisodeResource::collection($this->episodes),
        ];
    }
}
