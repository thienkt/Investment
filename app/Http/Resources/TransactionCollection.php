<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\ResourceCollection;

class TransactionCollection extends ResourceCollection
{
    /**
     * Transform the resource collection into an array.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return array|\Illuminate\Contracts\Support\Arrayable|\JsonSerializable
     */
    public function toArray($request)
    {
        return $this->collection->map(function ($data) {
            return [
                "reference_number" => $data->id,
                'transfer_amount' => $data->amount,
                "user_package_id" => $data->user_package_id,
                "transaction_type" => $data->type,
                "payment_status" => $data->status,
                "updated_at" => formatDate($data->updated_at),
                "created_at" => formatDate($data->created_at),
            ];
        });
    }
}
