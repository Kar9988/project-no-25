<?php

namespace App\Http\Resources;

use App\Policies\UserPolicy;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

/**
 * @method isLikedByUser($id)
 */
class EpisodeResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        $user = auth()->user();

        return [
            'id'          => $this->id,
            'title'       => $this->title,
            'thumb'       => $this->thumb_path,
            'source'      => $this->source_path,
            'position'    => $this->position,
            'duration'    => $this->duration,
            'can_see'     => UserPolicy::canViewEpisode($user, $this->id),
            'views_count' => $this->views_count,
            'likes_count' => $this->likes_count,
            'price'       => $this->price ?? 0,
            'video_id'    => $this->video_id,
            'liked'       => $this->isLikedByUser($user->id),
        ];
    }
}
