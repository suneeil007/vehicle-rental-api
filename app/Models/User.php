<?php

namespace App\Models;

use Database\Factories\UserFactory;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Attributes\Hidden;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;

use App\Modules\Role\Models\Role;
use App\Modules\Branch\Models\Branch;
use App\Modules\User\Models\UserProfile;
use App\Modules\Trip\Models\Trip;
use App\Modules\Booking\Models\Booking;

use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Database\Eloquent\Relations\HasMany;

#[Fillable([
    'name',
    'email',
    'slug',
    'phone',
    'password',
    'role_id',
    'branch_id',
    'status'
])]

#[Hidden([
    'password',
    'remember_token'
])]

class User extends Authenticatable
{
    use HasApiTokens, HasFactory, Notifiable;

    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
            'status' => 'string',
        ];
    }

    /*
    |--------------------------------------------------------------------------
    | Core Relationships
    |--------------------------------------------------------------------------
    */

    public function role(): BelongsTo
    {
        return $this->belongsTo(Role::class);
    }

    public function branch(): BelongsTo
    {
        return $this->belongsTo(Branch::class);
    }

    public function profile(): HasOne
    {
        return $this->hasOne(
            UserProfile::class,
            'user_id',
            'id'
        );
    }

    /*
    |--------------------------------------------------------------------------
    | Trip Relationships
    |--------------------------------------------------------------------------
    */

    /**
     * Trips where this user is the customer.
     */
    public function customerTrips(): HasMany
    {
        return $this->hasMany(
            Trip::class,
            'customer_id',
            'id'
        );
    }

    /**
     * Trips where this user is assigned as driver.
     */
    public function driverTrips(): HasMany
    {
        return $this->hasMany(
            Trip::class,
            'driver_id',
            'id'
        );
    }

    /**
     * Trips handed over by this staff user.
     */
    public function pickupTrips(): HasMany
    {
        return $this->hasMany(
            Trip::class,
            'pickup_staff_id',
            'id'
        );
    }

    /**
     * Trips received back by this staff user.
     */
    public function returnTrips(): HasMany
    {
        return $this->hasMany(
            Trip::class,
            'return_staff_id',
            'id'
        );
    }

    /**
     * Trips cancelled by this user.
     */
    public function cancelledTrips(): HasMany
    {
        return $this->hasMany(
            Trip::class,
            'cancelled_by',
            'id'
        );
    }

    /*
    |--------------------------------------------------------------------------
    | Role Helpers
    |--------------------------------------------------------------------------
    */

    /**
     * Check if the user has any of the given role slugs.
     */
    public function hasRole(string ...$slugs): bool
    {
        return in_array($this->role?->slug, $slugs, true);
    }

    /**
     * Convenience helper for the highest-privilege role.
     */
    public function isSuperAdmin(): bool
    {
        return $this->hasRole('super-admin');
    }

    /**
     * Check if the user is a driver.
     */
    public function isDriver(): bool
    {
        return $this->hasRole('driver');
    }

    /**
     * Check if the user is staff/admin/super-admin.
     */
    public function isStaff(): bool
    {
        return $this->hasRole(
            'staff',
            'admin',
            'super-admin'
        );
    }

    /*
    |--------------------------------------------------------------------------
    | Route Model Binding
    |--------------------------------------------------------------------------
    */

    public function getRouteKeyName(): string
    {
        return 'slug';
    }



    /**
     * Bookings made by this customer.
     */
    public function bookings(): HasMany
    {
        return $this->hasMany(
            Booking::class,
            'customer_id',
            'id'
        );
    }

    /**
     * Bookings approved by this staff/admin user.
     */
    public function approvedBookings(): HasMany
    {
        return $this->hasMany(
            Booking::class,
            'approved_by',
            'id'
        );
    }
}