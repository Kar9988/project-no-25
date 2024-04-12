<?php

namespace App\Http\Resources;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class PurchaseHistoryResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return [
            'coin'         => $this->points,
            'date'         => $this->created_at,
            'type'         => $this->type,
            'price'        => $this->price,
            'payment_type' => $this->plan_type === 'one_time' ? 'coin' : 'subscription',
            'payment_name' => $this->payment_name
        ];
    }
}
