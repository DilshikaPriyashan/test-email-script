<?php

namespace App\Traits;

use Illuminate\Http\Response;

trait AppTrait
{
    public function errorApiResponse($message = 'Something went wrong', $code = Response::HTTP_INTERNAL_SERVER_ERROR, $errors = [])
    {
        if (is_string($code) || ! ($code >= 400 && $code <= 599)) {
            $responseCode = Response::HTTP_INTERNAL_SERVER_ERROR;
        } else {
            $responseCode = $code;
        }

        $errorResponse = [
            'status' => 'error',
            'message' => $message,
            'code' => $code,
            'data' => $errors,
        ];

        return response()->json($errorResponse, $responseCode);
    }

    public function successApiResponse($message = null, $code = Response::HTTP_OK, $data = [])
    {
        $successResponse = [
            'status' => 'success',
            'data' => $data,
        ];

        if ($message !== null) {
            $successResponse['message'] = $message;
        }

        return response()->json($successResponse, $code);
    }
}
