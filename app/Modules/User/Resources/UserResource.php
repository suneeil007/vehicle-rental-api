<?php

namespace App\Modules\User\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class UserResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [

            'id' => $this->id,
            'slug' => $this->slug,
            'name' => $this->name,
            'email' => $this->email,
            'phone' => $this->phone,
            'status' => $this->status,
            'role' => $this->whenLoaded('role'),
            'branch' => $this->whenLoaded('branch'),

            'profile' => $this->whenLoaded('profile', function () {
                return [
                    'date_of_birth' => $this->profile->date_of_birth,
                    'gender' => $this->profile->gender,
                    'profile_photo' => $this->profile->profile_photo,
                    'citizenship_no' => $this->profile->citizenship_no,
                    'passport_no' => $this->profile->passport_no,
                    'driving_license_no' => $this->profile->driving_license_no,
                    'license_expiry' => $this->profile->license_expiry,
                    'nationality' => $this->profile->nationality,
                    'address' => $this->profile->address,
                    'city' => $this->profile->city,
                    'state' => $this->profile->state,
                    'country' => $this->profile->country,
                    'postal_code' => $this->profile->postal_code,
                    'emergency_contact_name' => $this->profile->emergency_contact_name,
                    'emergency_contact_phone' => $this->profile->emergency_contact_phone,
                    'bio' => $this->profile->bio,
                ];
            }),

            'created_at' => $this->created_at,
            'updated_at' => $this->updated_at,
        ];
    }
}