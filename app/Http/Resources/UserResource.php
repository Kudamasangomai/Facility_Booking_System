<?php

namespace App\Http\Resources;

use App\Enum\UserType;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class UserResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        $user = $request->user(); // Get the authenticated user
        return [

            'id' =>  $this->id,
            'name' => $this->name,
            'email' => $this->email,
            'companyname' => $this->companyname,
            'contact' => $this->contact,
            'active' => $this->when(
                $user->usertype === UserType::Admin->value ,
                $this->active
            ),


        ];
    }
}
