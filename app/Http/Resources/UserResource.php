<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class UserResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return array|\Illuminate\Contracts\Support\Arrayable|\JsonSerializable
     */
    public function toArray($request)
    {
        return removeNullish([
            'name' => $this->name,
            'email' => $this->email,
            'address' => $this->address,
            'identity_number' => $this->identity_number,
            'dob' => $this->dob,
            'gender' => $this->gender,
            'avatar' => $this->avatar,
            'phone_number' => $this->phone_number,
            'is_verify' => $this->is_verify,
            'portrait' => $this->portrait,
            'identity_image_front' => $this->identity_image_front,
            'identity_image_back' => $this->identity_image_back,
        ]);
    }
}
