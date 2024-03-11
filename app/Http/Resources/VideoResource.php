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
            'id' => $this->id,
            'description' => $this->description,
            'title'  => $this->title,
            'cover_img' => $this->cover_img,
            'category' => $this->category->name,
            'category_id' => $this->category_id,
            'episodes' => $this->episodes,
            'likes_count' => $this->likes_count,
            'episode_likes_count' => $this->likes,
        ];
    }
}
