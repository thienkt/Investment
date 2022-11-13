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
        return [
            'name' => $this->name,
            'email' => $this->email,
            'address' => $this->address,
            'identity_number' => $this->identity_number,
            'dob' => $this->dob,
            'gender' => $this->gender,
            'avatar' => $this->avatar,
            'phone_number' => $this->phone_number,
            'is_verify' => $this->is_verify,
            'is_activated' => (bool)$this->email_verified_at,
            'portrait' => $this->portrait,
            'identity_image_front' => $this->identity_image_front,
            'identity_image_back' => $this->identity_image_back,
            'issue_place' => $this->issue_place,
            'issue_date' => $this->issue_date,
            'valid_date' => $this->valid_date,
        ];
    }
}
