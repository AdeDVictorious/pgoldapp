<?php

namespace App\Traits;


trait HttpResponses
{
    /**
     * Success response
     */
    protected function success($data, $message = null, $code = 200)
    {
        return response()->json([
            'status' => 'ok',
            'message' => $message,
            'data' => $data
        ], $code);
    }


    /**
     * Error response
     */
    protected function error($data, $message = null, $code)
    {
        return response()->json([
            'status' => 'failed',
            'message' =>  $message,
            'data' => $data
        ], $code);
    }
}
