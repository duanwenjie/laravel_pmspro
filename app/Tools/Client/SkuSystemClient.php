<?php

namespace App\Tools\Client;

use App\Exceptions\InvalidRequestException;
use Illuminate\Support\Facades\Http;

class SkuSystemClient
{
    public static function call($uri, $param, $method = 'post')
    {
        $response = Http::retry(3, 1000)->timeout(20)->$method(
            config('host.sku_system.'.config('app.env')).$uri,
            $param
        );
        $result = $response->json();
        if (!isset($result['code']) && !isset($result['state'])) {
            throw new InvalidRequestException('调用SkuSystem失败:'.($result['msg'] ?? ''));
        }
        return $result;
    }
}
