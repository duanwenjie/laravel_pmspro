<?php


namespace App\Tools;

use App\Exceptions\InternalException;
use App\Exceptions\InvalidRequestException;
use Exception;
use Illuminate\Support\Facades\Log;
use Motan\Client;
use Motan\URL;

class Motan
{
    //rpc 调用的核心方法
    public static function call($action, $param, $url, $version = '1.0')
    {
        $url = new URL($url);
        $url->setVersion($version);
        $client = new Client($url);
        try {
            $result = $client->$action(json_encode($param));
        } catch (Exception $e) {
            Log::error("Motan:{$action}失败,msg:{$e->getMessage()}");
            throw new InternalException($e->getMessage());
        }
        $result = json_decode($result, true);
        if (!isset($result['state']) || $result['state'] != '000001') {
            throw new InvalidRequestException("Motan:{$action}:".($result['msg'] ?? '请求失败'));
        }
        return $result['data'] ?? [];
    }
}
