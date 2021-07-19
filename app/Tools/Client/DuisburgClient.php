<?php

namespace App\Tools\Client;

use App\Exceptions\InvalidRequestException;
use Illuminate\Support\Facades\Http;

class DuisburgClient
{
    public static function call($uri, $param, $method = 'post')
    {
        $response = Http::timeout(600)->asForm()->$method(config('host.duisburg.'.config('app.env')).$uri, $param);
        $result = $response->json();
        if (!isset($result['error']) && !isset($result['error_message'])) {
            throw new InvalidRequestException('调用duisburg失败:'.($result['msg'] ?? ''));
        }
        return $result;
    }
}
