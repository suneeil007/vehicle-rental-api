<?php

namespace App\Modules\Vehicle\Models;

use App\Modules\Booking\Models\Booking;
use App\Modules\Branch\Models\Branch;
use App\Modules\Vehicle\Enums\FuelType;
use App\Modules\Vehicle\Enums\TransmissionType;
use App\Modules\Vehicle\Enums\VehicleStatus;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Vehicle extends Model
{
    use HasFactory;

    protected $table = 'vehicles';

    protected $fillable = [
        'vehicle_category_id',
        'branch_id',
        'name',
        'slug',
        'brand',
        'model',
        'manufacture_year',
        'transmission',
        'fuel_type',
        'seat_capacity',
        'price_per_day',
        'registration_number',
        'mileage',
        'color',
        'description',
        'status',
    ];

    protected $casts = [
        'transmission' => TransmissionType::class,
        'fuel_type'    => FuelType::class,
        'status'       => VehicleStatus::class,
        'price_per_day'=> 'decimal:2',
    ];

    public function category(): BelongsTo
    {
        return $this->belongsTo(
            VehicleCategory::class,
            'vehicle_category_id'
        );
    }

    public function images(): HasMany
    {
        return $this->hasMany(VehicleImage::class);
    }
    
    public function getRouteKeyName(): string
    {
        return 'slug';
    }

    public function bookings()
    {
        return $this->hasMany(
            Booking::class,
            'vehicle_id',
            'id'
        );
    }

    public function branch(): BelongsTo
    {
        return $this->belongsTo(
            Branch::class,
            'branch_id',
            'id'
        );
    }
    
}