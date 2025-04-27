<?php

namespace App\Http\Resources;

use App\Enum\UserType;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class FacilityResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {

        return[
            'id'=> $this->id,
            'name'=> $this->name,
            'description' => $this->description,
            'capacity' => $this->capacity,
            'price' => $this->price ,
            'specialnote' => $this->specialnote,
            'images' => new ImageResource($this->whenLoaded('images')),
            'booking' => new BookingCollection($this->whenLoaded('bookings'))

        ];
    }
}
