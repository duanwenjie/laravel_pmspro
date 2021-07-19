<?php

namespace App\Tools\Client;

use App\Exceptions\InvalidRequestException;
use Illuminate\Support\Facades\Http;

class HzErpClient
{
    public static function call($uri, $param, $method = 'post')
    {
        $response = Http::retry(3, 1000)->timeout(20)->$method(
            config('host.hz_new_erp.'.config('app.env')).$uri,
            $param
        );
        $result = $response->json();
        if (!isset($result['code']) && !isset($result['state'])) {
            throw new InvalidRequestException('调用汇总进销存失败:'.$uri.'失败,'.($result['msg'] ?? ''));
        }
        return $result;
    }
}
