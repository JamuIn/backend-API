<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class CartResource extends JsonResource
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
            'id' => $this->id,
            'user_id' => $this->user_id,
            'product_id' => $this->product_id,
            'product_name' => $this->product->name,
            'product_description' => $this->product->description,
            'main_ingredient' => $this->product->ingredients->first()->name,
            'quantity' => $this->quantity,
            'price' => $this->product->price * $this->quantity
        ];
    }
}
