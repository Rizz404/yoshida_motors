<?php

namespace App\Traits;

use Illuminate\Http\JsonResponse;

trait ApiResponseTrait
{
    /**
     * Success Response
     */
    protected function successResponse($data = null, string $message = 'Success', int $statusCode = 200): JsonResponse
    {
        return response()->json([
            'success' => true,
            'message' => $message,
            'data' => $data,
        ], $statusCode);
    }

    /**
     * Cursor Paginated Response
     * Khusus buat infinite scroll, biar frontend gampang ambil next_cursor-nya!
     */
    protected function cursorPaginatedResponse($data, string $message = 'Success'): JsonResponse
    {
        return response()->json([
            'success' => true,
            'message' => $message,
            'data' => $data->items(), // Isinya array datanya doang
            'meta' => [
                'per_page' => $data->perPage(),
                'next_cursor' => $data->nextCursor()?->encode(), // Kunci buat load more!
                'prev_cursor' => $data->previousCursor()?->encode(),
                'has_more' => $data->hasMorePages(),
            ],
        ], 200);
    }

    /**
     * Error Response
     */
    protected function errorResponse(string $message = 'Error occurred', $errors = null, int $statusCode = 400): JsonResponse
    {
        return response()->json([
            'success' => false,
            'message' => $message,
            'errors' => $errors,
        ], $statusCode);
    }

    /**
     * Validation Error Response
     */
    protected function validationErrorResponse($errors, string $message = 'Validation failed'): JsonResponse
    {
        return response()->json([
            'success' => false,
            'message' => $message,
            'errors' => $errors,
        ], 422);
    }

    /**
     * Not Found Response
     */
    protected function notFoundResponse(string $message = 'Resource not found'): JsonResponse
    {
        return response()->json([
            'success' => false,
            'message' => $message,
            'errors' => null,
        ], 404);
    }

    /**
     * Unauthorized Response
     */
    protected function unauthorizedResponse(string $message = 'Unauthorized'): JsonResponse
    {
        return response()->json([
            'success' => false,
            'message' => $message,
            'errors' => null,
        ], 401);
    }
}
