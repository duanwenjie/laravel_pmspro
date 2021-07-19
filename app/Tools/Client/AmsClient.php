<?php
/**
 *
 * Class AmsClient
 * @author jip
 * @time 2020/12/28 18:59
 */

namespace App\Tools\Client;

use App\Exceptions\InternalException;
use App\Exceptions\InvalidRequestException;
use App\Tools\Motan;

class AmsClient
{
    /**
     * 获取帐号列表
     * @param $platformCode
     * @param $pullTimeStart
     * @param $pullTimeEnd
     * @param  int  $pageNo
     * @param  int  $pageSize
     * @return array
     * @throws InternalException
     * @throws InvalidRequestException
     * @author jip
     * @time 2020/12/28 20:37
     */
    public static function getAccounts($platformCode, $pullTimeStart, $pullTimeEnd, $pageNo = 1, $pageSize = 100)
    {
        $url = config('host.iactmgr_rpc.'.config('app.env'));
        $param = [
            "platformCode"    => $platformCode,
            "modifyDateStart" => $pullTimeStart,
            "modifyDateEnd"   => $pullTimeEnd,
            "pageNo"          => $pageNo,
            "pageSize"        => $pageSize // 默认每页获取100条
        ];
        return Motan::call('getAccountInfo', $param, $url);
    }

    /**
     * Introduction
     * @param $accountIds
     * @return array
     * @throws InternalException
     * @throws InvalidRequestException
     * @author jip
     * @time 2021/2/2 14:10
     */
    public static function getFbaAccountSecret($accountIds)
    {
        $url = config('host.iactmgr_rpc.'.config('app.env'));
        $param = [
            "accountIds" => $accountIds,
        ];
        return Motan::call('getFbaAwsAccessInfo', $param, $url);
    }
}
