<?php

namespace App\Http\Controllers;

use Illuminate\Http\JsonResponse;

abstract class ApiController extends Controller
{
    /**
     * Return a success JSON response.
     */
    protected function success($data = null, string $message = 'Success', int $status = 200): JsonResponse
    {
        $response = [
            'message' => $message,
        ];

        if ($data !== null) {
            $response['data'] = $data;
        }

        return response()->json($response, $status);
    }

    /**
     * Return an error JSON response.
     */
    protected function error(string $message = 'Error', array $errors = [], int $status = 400): JsonResponse
    {
        $response = [
            'error' => [
                'message' => $message,
            ],
        ];

        if (!empty($errors)) {
            $response['error']['errors'] = $errors;
        }

        return response()->json($response, $status);
    }

    /**
     * Return a paginated JSON response.
     */
    protected function paginated($data, string $message = 'Success'): JsonResponse
    {
        return response()->json([
            'message' => $message,
            'data' => $data->items(),
            'meta' => [
                'pagination' => [
                    'current_page' => $data->currentPage(),
                    'last_page' => $data->lastPage(),
                    'per_page' => $data->perPage(),
                    'total' => $data->total(),
                    'from' => $data->firstItem(),
                    'to' => $data->lastItem(),
                ],
            ],
        ]);
    }
}

