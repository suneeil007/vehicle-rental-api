<?php

namespace App\Modules\Vehicle\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class VehicleImageResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     */
    public function toArray(Request $request): array
    {
        return [
            'id'          => $this->id,
            'vehicle_id'  => $this->vehicle_id,
            'image'       => asset($this->image),
            'is_featured' => $this->is_featured,
            'sort_order'  => $this->sort_order,
        ];
    }
}