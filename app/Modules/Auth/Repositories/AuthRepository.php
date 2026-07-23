<?php

namespace App\Modules\Auth\Repositories;


use App\Models\User;
use Illuminate\Support\Facades\Hash;
use App\Modules\Role\Models\Role;
use App\Modules\Auth\Repositories\Contracts\AuthRepositoryInterface;


class AuthRepository implements AuthRepositoryInterface
{

    public function create(array $data): User
    {

        $customerRole = Role::where(
            'slug',
            'customer'
        )->first();


        return User::create([

            'name' => $data['name'],

            'slug' => $data['slug'],

            'email' => $data['email'],

            'phone' => $data['phone'] ?? null,

            'password' => Hash::make(
                $data['password']
            ),


            // automatically assign customer
            'role_id' => $data['role_id'] 
                ?? $customerRole->id,


            'branch_id' => $data['branch_id'] ?? null,


            'status' => 'active',

        ]);

    }


    public function findByEmail(string $email): ?User
    {
        return User::where(
            'email',
            $email
        )->first();
    }

}