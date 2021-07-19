<?php
/**
 *
 * Class OmsClient
 * @author jip
 * @time 2020/12/28 18:59
 */

namespace App\Tools\Client;

use App\Tools\Formater;
use Illuminate\Support\Facades\Http;

class OmsClient
{
    /**
     * 获取帐号TOKEN
     * @param $platformCode
     * @param  array  $accounts
     * @return array
     * @author jip
     * @time 2020/12/28 19:29
     */
    public static function getAccountToken($platformCode, $accounts = [])
    {
        $postData = ['platformCode' => $platformCode];
        $accounts && $postData['accounts'] = Formater::multiValue($accounts);
        $response = Http::retry(
            3,
            1000
        )->timeout(20)->post(
            config('host.oms_http.'.config('app.env')).'api/account/token/getAccountToken',
            $postData
        );
        $response = $response->json();
        return !empty($response['data']) ? $response['data'] : [];
    }
}
