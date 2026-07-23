<?php

namespace App\Modules\Role\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use App\Models\User;

class Role extends Model
{
    protected $table = 'roles';


    protected $fillable = [
        'name',
        'slug',
        'description',
        'status',
    ];


    protected $casts = [
        'status' => 'boolean',
    ];


    public function users(): HasMany
    {
        return $this->hasMany(User::class);
    }
}