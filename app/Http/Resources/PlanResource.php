<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class PlanResource extends JsonResource
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
            'name' => $this->name,
            'price' => $this->price,
            'point' => $this->points,
            'description' => $this->description,
            'discount' => $this->discount,
            'created_at' => $this->created_at,
            'updated_at	' => $this->updated_at
        ];
    }
}
