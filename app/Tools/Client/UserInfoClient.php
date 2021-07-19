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

class UserInfoClient
{

    /**
     * Desc:发送消息
     * @param $param
     * @throws InvalidRequestException
     */
    public static function sendMessage($param)
    {
        $uri = '/api/1.0/dingding/message/user';
        self::call($uri, $param);
    }


    public static function getToken()
    {
        $username = 'opsuser';
        $password = 'yks147258369';
        $auth = array('username' => $username, 'password' => $password);
        $url = config('host.ysk_user_info.'.config('app.env'));
        $response = Http::asForm()->post($url.'/api/1.0/account/token', $auth);
        $result = $response->json();
        return $result['token'] ?? '';
    }

    public static function call($uri, $param, $method = 'post')
    {
        $token = self::getToken();
        $url = config('host.ysk_user_info.'.config('app.env'));
        $response = Http::retry(3, 1000)->withHeaders([
            'Authorization' => 'JWT '.$token,
            'Content-Type'  => 'application/json'
        ])->timeout(20)->$method($url.$uri, $param);
        $result = $response->json();
        if (!isset($result['status']) || $result['status'] != 'success') {
            throw new InvalidRequestException('用户中心:'.$result['message'] ?? '');
        }
        return $result['result'] ?? [];
    }
}
