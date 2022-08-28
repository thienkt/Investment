<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class UserStatusResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return array|\Illuminate\Contracts\Support\Arrayable|\JsonSerializable
     */
    public function toArray($request)
    {
        $twoFA = isset($this->two_factor_recovery_codes) && isset($this->two_factor_secret);

        return [
            'enabled_2fa' => $twoFA,
            'verified_email' => (bool) $this->email_verified_at,
            'verified_ekyc' => $this->is_verify,
        ];
    }
}
