<?php
/**
 * Description
 * User: dwj
 * Date: 2021/6/7
 * Time: 11:33 上午
 */

namespace App\Tools;

use App\Models\CurlApiLog;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Http\Request;

class ApiLog
{
    /**
     * API请求接口日志记录
     * @param  int  $type  接口类型
     * @param  int  $status  接口请求状态
     * @param  string  $message  接口返回消息
     * @param  array  $response  调用接口时的输出数据
     * @param  array  $request  调用接口时的请求数据
     * @param  string  $method  调用接口时的请求方法
     * @author dwj
     */
    public static function add(int $type, int $status, $message = '', $response = [], $request = [], $method = '')
    {
        if (empty($request)) {
            $request = request();
            if (empty($message)) {
                $message = ($status == CurlApiLog::success) ? ApiCode::$codeList[ApiCode::SUCCESS] : ApiCode::$codeList[ApiCode::HTTP_BAD_REQUEST];
            }
        }

        if (empty($response)) {
            $response = [
                'state'   => ($status == CurlApiLog::success) ? ApiCode::SUCCESS : ApiCode::HTTP_BAD_REQUEST,
                'message' => $message,
            ];
        }

        if ($request instanceof FormRequest || $request instanceof Request) {
            $method = (empty($method)) ? $request->path() : $method;
            $request = $request->all();
        }

        CurlApiLog::query()->insert([
            'type'     => $type,
            'status'   => $status,
            'method'   => $method,
            'message'  => $message,
            'request'  => json_encode($request, JSON_UNESCAPED_UNICODE),
            'response' => json_encode($response, JSON_UNESCAPED_UNICODE),
        ]);
    }
}
