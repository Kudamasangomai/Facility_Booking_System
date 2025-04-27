<?php

namespace App\Http\Resources;

use App\Enum\UserType;
use Illuminate\Http\Request;
use App\Http\Resources\UserResource;
use Illuminate\Support\Facades\Auth;
use Illuminate\Http\Resources\Json\JsonResource;

class BookingResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        $user = Auth::user();

        return [
            'id' => $this->when(
                $user->usertype === UserType::Admin->value ||
                $user->id === $this->user_id, $this->id),

            'user' => $this->when(
                $user->usertype === UserType::Admin->value ||
                $user->id === $this->user_id,
                new UserResource($this->whenLoaded('user'))
            ),

            'facility' => new FacilityResource($this->whenLoaded('facility')),
            'check_in' => $this->check_in,
            'check_out' => $this->check_out,
            'attendents' => $this->when(
                $user->usertype === UserType::Admin->value || $user->id === $this->user_id,
                $this->attendants
            ),
            'purpose' =>  $this->when(
                $user->usertype === UserType::Admin->value || $user->id === $this->user_id,
                $this->purpose
            ),

        ];
    }
}
