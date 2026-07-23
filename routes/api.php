<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

Route::get('/user', function (Request $request) {
    return $request->user();
})->middleware('auth:sanctum');

Route::middleware('auth:sanctum')->get('/logged-in-user', function (Request $request) {
    $user = $request->user()->load([
        'role',
        'branch',
        'profile',
    ]);

    return response()->json([
        'success' => true,
        'message' => 'Authenticated user fetched successfully.',
        'data' => [
            'id' => $user->id,
            'slug' => $user->slug,
            'name' => $user->name,
            'email' => $user->email,
            'phone' => $user->phone,
            'status' => $user->status,

            'role' => $user->role ? [
                'id' => $user->role->id,
                'name' => $user->role->name,
                'slug' => $user->role->slug,
            ] : null,

            'branch' => $user->branch ? [
                'id' => $user->branch->id,
                'name' => $user->branch->name,
                'slug' => $user->branch->slug,
            ] : null,

            'profile' => $user->profile ? [
                'nationality' => $user->profile->nationality,
                'city' => $user->profile->city,
                'country' => $user->profile->country,
                'profile_photo' => $user->profile->profile_photo,
            ] : null,
        ],
    ]);
});