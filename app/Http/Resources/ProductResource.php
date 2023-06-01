<?php

namespace App\Http\Resources;

use App\Models\RekomendasiJamu\Ingredient;
use Illuminate\Http\Resources\Json\JsonResource;

class ProductResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return array|\Illuminate\Contracts\Support\Arrayable|\JsonSerializable
     */
    public function toArray($request)
    {
        $ingredient = $this->ingredients()->first();
        if (!$ingredient == null) {
            $ingredient = $ingredient->name;
        }
        return [
            'jamu_category_id' => $this->jamu_category_id,
            'main_ingredient' => $ingredient,
            'name' => $this->name,
            'description' => $this->description,
            'image' => $this->image,
            'price' => $this->price,
            'stock' => $this->stock,
            'created_at' => $this->created_at,
            'updated_at' => $this->updated_at
        ];
    }
}
