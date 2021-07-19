<?php

namespace App\Tools\Client;

use App\Exceptions\InvalidRequestException;
use App\Models\CurlApiLog;
use App\Tools\ApiLog;
use Illuminate\Support\Facades\Http;

class PmsClient
{
    public static function call($url, $param, $method = 'post')
    {
        $response = Http::withHeaders([
                'Content-Type'  => 'application/json'
            ])
            ->timeout(20)->$method(
            config('host.pms.'.config('app.env')).$url,
            $param
        );

        $result = $response->json();
        if (!isset($result['code']) && !isset($result['state'])) {
            $errorMsg = '调用PMS失败:'.$url.'失败,'.($result['msg'] ?? '');
            ApiLog::add(CurlApiLog::pushPmsPrType, CurlApiLog::error, $errorMsg, $result, $param, $url);
            throw new InvalidRequestException($errorMsg);
        }

        ApiLog::add(CurlApiLog::pushPmsPrType, CurlApiLog::success, '调用PMS接口成功', $result, $param, $url);
        return $result;
    }
}
