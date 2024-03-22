<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class UserRewardResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {

        return [
            'id'        => $this->id,
            'name'      => $this->reward->name,
            'reward_id' => $this->reward->id,
            'user_id'   => $this->user_id,
            'bonus'     => $this->reward->bonus,
            'date'      => $this->created_at->toDateString()
        ];
    }
}
