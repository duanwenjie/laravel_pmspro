<?php
/**
 * 新品模块服务
 * Class IpimClient
 * @author jip
 * @time 2020/12/28 18:59
 */

namespace App\Tools\Client;

use App\Exceptions\InternalException;
use App\Exceptions\InvalidRequestException;
use App\Tools\Motan;
use Illuminate\Support\Facades\Auth;

class IpimClient
{
    /**
     * 团队配置列表
     * @param $startTime
     * @param $endTime
     * @param  int  $pageNo
     * @param  int  $pageSize
     * @return array
     * @throws InternalException
     * @throws InvalidRequestException
     * @author jip
     * @time 2021/1/2 16:52
     */
    public static function getFbaTeamConfigList($startTime, $endTime, $pageNo = 1, $pageSize = 100)
    {
        $user = Auth::user();
        $url = config('host.ipimfba_rpc.'.config('app.env'));
        $param = [
            'operator' => $user->username ?? 'fbaerp',
            'data'     => [
                'pageNumber' => $pageNo,
                'pageData'   => $pageSize,
                'startTime'  => $startTime,
                'endTime'    => $endTime
            ]
        ];
        $result = Motan::call('getFbaTeamConfigBulking', $param, $url);
        return $result;
    }
}
