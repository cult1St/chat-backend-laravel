<?php

namespace App;

trait ApiResponder
{
    

    public function successResponse($data, $message = "Request was successful", $code = 200)
    {
        return response()->json([
            'success' => true,
            'message' => $message,
            'data' => $data,
            'status' => $code
        ], $code);
    }
    public function errorResponse($message = "An error occurred", $code = 500, $data = null)
    {
        return response()->json([
            'success' => false,
            'message' => $message,
            'errors' => $data,
            'status' => $code
        ], $code);
    }
}
