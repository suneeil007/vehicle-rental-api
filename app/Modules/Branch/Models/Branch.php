<?php

namespace App\Modules\Branch\Models;

use App\Models\User;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Branch extends Model
{
    use HasFactory;

    protected $fillable = [

        'slug',
        'name',
        'code',
        'phone',
        'email',
        'address',
        'city',
        'state',
        'country',
        'postal_code',
        'latitude',
        'longitude',
        'opening_time',
        'closing_time',
        'manager_name',
        'manager_phone',
        'status',
    ];

    protected $casts = [

        'latitude' => 'float',
        'longitude' => 'float',
        'opening_time' => 'datetime:H:i',
        'closing_time' => 'datetime:H:i',

    ];

    /**
     * Route Model Binding by UUID.
     */
    public function getRouteKeyName(): string
    {
        return 'slug';
    }

    /**
     * Branch has many users.
     */
    public function users()
    {
        return $this->hasMany(
            User::class,
            'branch_id',
            'id'
        );
    }

    /**
     * Branch has many vehicles.
     */
    public function vehicles()
    {
        return $this->hasMany(
            \App\Modules\Vehicle\Models\Vehicle::class,
            'branch_id',
            'id'
        );
    }
}