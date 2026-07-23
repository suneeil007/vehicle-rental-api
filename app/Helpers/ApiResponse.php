<?php

namespace App\Helpers;

use Illuminate\Http\JsonResponse;

class ApiResponse
{
    /**
     * Success Response.
     */
    public static function success(
        mixed $data = null,
        string $message = 'Success',
        int $status = 200
    ): JsonResponse {

        return response()->json([
            'success' => true,
            'message' => $message,
            'data' => $data,
        ], $status);

    }

    /**
     * Created Response.
     */
    public static function created(
        mixed $data = null,
        string $message = 'Created successfully.'
    ): JsonResponse {

        return self::success(
            $data,
            $message,
            201
        );

    }

    /**
     * Updated Response.
     */
    public static function updated(
        mixed $data = null,
        string $message = 'Updated successfully.'
    ): JsonResponse {

        return self::success(
            $data,
            $message,
            200
        );

    }

    /**
     * Deleted Response.
     */
    public static function deleted(
        string $message = 'Deleted successfully.'
    ): JsonResponse {

        return self::success(
            null,
            $message,
            200
        );

    }

    /**
     * Error Response.
     */
    public static function error(
        string $message,
        int $status = 400,
        mixed $errors = null
    ): JsonResponse {

        return response()->json([
            'success' => false,
            'message' => $message,
            'errors' => $errors,
        ], $status);

    }
}