<?php

namespace App\Traits;

use App\Tools\ApiCode;
use Illuminate\Http\JsonResponse;

trait ApiResponse
{
    /**
     * 200
     *
     * @param  mixed  $data
     * @param  string  $msg
     * @return JsonResponse
     * @author springlee<jslichun@sina.com>
     */
    protected function success($msg = '', $data = [])
    {
        return response()->json([
            'state' => ApiCode::SUCCESS,
            'msg'   => $msg ? $msg : ApiCode::$codeList[ApiCode::SUCCESS],
            'data'  => $data
        ]);
    }

    /**
     * 200
     *
     * @param  string  $msg
     * @param  mixed  $data
     * @return JsonResponse
     * @author springlee<jslichun@sina.com>
     */
    protected function successForResource($data, $msg = '')
    {
        return response()->json([
            'state' => '000001',
            'msg'   => $msg ? $msg : ApiCode::$codeList[ApiCode::SUCCESS],
            'data'  => $data
        ]);
    }

    /**
     * 200
     *
     * @param  string  $msg
     * @param  mixed  $data
     * @return JsonResponse
     * @author springlee<jslichun@sina.com>
     */
    protected function successForResourcePage($data, $msg = '')
    {
        return response()->json([
            'state' => '000001',
            'msg'   => $msg ? $msg : ApiCode::$codeList[ApiCode::SUCCESS],
            'data'  => array_merge([
                'list'  => $data,
                'total' => $data->total(),
            ])
        ]);
    }
}
