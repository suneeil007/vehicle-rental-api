<?php

namespace App\Modules\Vehicle\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class VehicleResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     */
    public function toArray(Request $request): array
    {
        return [

            'id' => $this->id,
            'category' => [
                'id' => $this->category?->id,
                'name' => $this->category?->name,
                'slug' => $this->category?->slug,
            ],
            'name' => $this->name,
            'slug' => $this->slug,
            'brand' => $this->brand,
            'model' => $this->model,
            'manufacture_year' => $this->manufacture_year,
            'transmission' => $this->transmission,
            'fuel_type' => $this->fuel_type,
            'seat_capacity' => $this->seat_capacity,
            'price_per_day' => $this->price_per_day,
            'registration_number' => $this->registration_number,
            'mileage' => $this->mileage,
            'color' => $this->color,
            'description' => $this->description,
            'status' => $this->status,
            'images' => $this->images->map(function ($image) {
                return [
                    'id' => $image->id,
                    'image' => asset('storage/'.$image->image),
                    'is_featured' => $image->is_featured,
                    'sort_order' => $image->sort_order,
                ];
            }),
            'created_at' => $this->created_at?->format('Y-m-d H:i:s'),
            'updated_at' => $this->updated_at?->format('Y-m-d H:i:s'),
        ];
    }
}