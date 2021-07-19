<?php

namespace App\Tools\Client;

use App\Exceptions\InvalidRequestException;
use Illuminate\Support\Facades\Http;

class OverseawmsClient
{
    public static function call($uri, $param, $method = 'post')
    {
        $header = [
            'appkey'   => '6hVxcxnXfSutI57Q',
            'apptoken' => 'Q1EVxkMT0Km8c7JoOVu6LQgVK2qNMHbN'
        ];
        $response = Http::timeout(20)->withHeaders($header)->$method(
            config('host.overseawms.'.config('app.env')).$uri,
            $param
        );
        $result = $response->json();
        if (!isset($result['code']) && !isset($result['state'])) {
            throw new InvalidRequestException('调用overseawms失败:'.($result['msg'] ?? ''));
        }
        return $result;
    }
}
