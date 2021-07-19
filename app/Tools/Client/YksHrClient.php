<?php
/**
 * 新品模块服务
 * Class IpimClient
 * @author jip
 * @time 2020/12/28 18:59
 */

namespace App\Tools\Client;

use App\Exceptions\InvalidRequestException;
use Illuminate\Support\Facades\Http;

class YksHrClient
{


    /**
     * Desc:获取用户列表
     * @param $param
     * @return mixed
     * @throws InvalidRequestException
     */
    public static function getUserList($param)
    {
        $uri = 'openapi/employee/getList';
        return self::call($uri, $param);
    }


    public static function call($uri, $param, $method = 'post')
    {
        $token = "NjAwMTBiNzIxZTQ5NDphM2Q5YWIwOWQyMTgzY2Q5NzJkMDAyNThmNzY0OTdlYg";
        $url = config('host.ysk_hr.'.config('app.env'));
        $response = Http::retry(3, 1000)->withHeaders(['yksToken' => $token])->timeout(20)->$method($url.$uri, $param);
        $result = $response->json();
        if (!isset($result['state']) || $result['state'] != '000001') {
            throw new InvalidRequestException('调用人事系统失败:'.$result['msg'] ?? '');
        }
        return $result['data']['list'] ?? [];
    }
}
