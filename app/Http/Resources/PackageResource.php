<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class PackageResource extends JsonResource
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
            'avatar' => $this->avatar ?? Config('package.default_avatar'),
            'investment_amount' => $this->investment_amount ?? 0,
            'is_default' => $this->is_default,
            'name' => $this->name,
        ];
    }
}
