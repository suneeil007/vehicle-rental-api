<?php

namespace App\Modules\Auth\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class UserProfileResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     */
    public function toArray(Request $request): array
    {
        return [
            'slug' => $this->slug,
            'user_slug' => $this->user?->slug,

            'date_of_birth' => $this->date_of_birth,
            'gender' => $this->gender,

            'profile_photo' => $this->profile_photo,

            'citizenship_no' => $this->citizenship_no,
            'passport_no' => $this->passport_no,
            'driving_license_no' => $this->driving_license_no,
            'license_expiry' => $this->license_expiry,

            'nationality' => $this->nationality,

            'address' => $this->address,
            'city' => $this->city,
            'state' => $this->state,
            'country' => $this->country,
            'postal_code' => $this->postal_code,

            'emergency_contact_name' => $this->emergency_contact_name,
            'emergency_contact_phone' => $this->emergency_contact_phone,

            'bio' => $this->bio,

            'created_at' => $this->created_at,
            'updated_at' => $this->updated_at,
        ];
    }
}