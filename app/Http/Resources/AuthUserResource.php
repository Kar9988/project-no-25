<?php

namespace App\Http\Resources;

use App\Policies\UserPolicy;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class AuthUserResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return [
            'id'              => $this->id,
            'first_name'      => $this->name,
            'email'           => $this->email,
            'balance'         => $this->userBalance->amount ?? 0,
            'bonus'           => $this->userBalance->bonus ?? 0,
            'subscription_id' => $this->getActiveSubscription->id ?? null,
            'active_plan'     => $this->getActiveSubscription?->plan ?? null,
            'role'            => $this->whenLoaded('role', function () {
                return $this->role->name;
            }),
        ];
    }
}
