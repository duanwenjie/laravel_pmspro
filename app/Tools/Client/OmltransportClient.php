<?php

namespace App\Tools\Client;

use App\Exceptions\InvalidRequestException;
use Illuminate\Support\Facades\Http;

class OmltransportClient
{
    public static function call($uri, $param, $method = 'post')
    {
        $response = Http::timeout(20)->$method(config('host.omltransport.'.config('app.env')).$uri, $param);
        $result = $response->json();
        if (!isset($result['code']) && !isset($result['state'])) {
            throw new InvalidRequestException('调用outmainlinetransport失败:'.($result['msg'] ?? ''));
        }
        return $result;
    }
}
