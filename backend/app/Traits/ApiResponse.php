<?php

namespace App\Traits;

trait ApiResponse
{
    protected function success($data = null, string $message = 'success')
    {
        return response()->json([
            'code' => 200,
            'message' => $message,
            'data' => $data,
        ]);
    }

    protected function paginate($list, array $meta)
    {
        return response()->json([
            'code' => 200,
            'message' => 'success',
            'data' => [
                'list' => $list,
                'meta' => $meta,
            ],
        ]);
    }

    protected function error(int $code, string $message, $errors = null)
    {
        $body = ['code' => $code, 'message' => $message];
        if ($errors !== null) {
            $body['errors'] = $errors;
        }
        return response()->json($body, $code);
    }
}
