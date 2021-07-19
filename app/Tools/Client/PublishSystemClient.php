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

class PublishSystemClient
{

    /**
     * Introduction
     * @param $method
     * @param $params
     * @return array
     * @throws InternalException
     * @throws InvalidRequestException
     * @author jip
     * @time 2021/2/2 14:10
     */
    public static function call($method, $params)
    {
        $url = config('host.iplsfacade_rpc.'.config('app.env'));
        return Motan::call($method, $params, $url);
    }
}
