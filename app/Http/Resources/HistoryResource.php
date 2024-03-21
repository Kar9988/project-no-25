<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class HistoryResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {

        return [
            'id'   =>  $this->id,
            'episode_id' => $this->episode_id,
            'user_id'    =>$this->user_id,
            'episode'   => new EpisodeResource($this->episode),
        ];
    }
}
