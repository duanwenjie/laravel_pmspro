<?php

namespace App\Tools\Client;

use App\Exceptions\InvalidRequestException;
use Illuminate\Support\Facades\Http;

class FbaWarehouseClient
{
    public static function call($uri, $param, $method = 'post')
    {
        $response = Http::timeout(20)->asForm()->$method(config('host.fbawarehouse.'.config('app.env')).$uri, $param);
        $result = $response->json();
        if (!isset($result['code']) && !isset($result['state'])) {
            throw new InvalidRequestException('调用fbawarehouse接口'.$uri.'失败:'.($result['msg'] ?? ''));
        }
        return $result;
    }

    /**
     * 根据采购单-获取fba中转仓出库数据
     * @Author   jiangshilin
     * @DateTime 2021-01-09
     * @param $puIds
     * @return array [type] [description]
     * @throws InvalidRequestException
     */
    public static function getFbaWarehouseOutData($puIds)
    {
        if (empty($puIds)) {
            return [];
        }
        $data['puOrd'] = json_encode($puIds);
        $res = self::call('Api/Amazon/Client/getFbaDelInfo', $data, 'post');
        $data = [];
        if (!empty($res['data']) && is_array($res['data'])) {
            foreach ($res['data'] as $value) {
                if (empty($value['puId'])) {
                    continue;
                }
                $puId = $value['puId'];
                $sku = $value['sku'];
                $data[$puId][$sku] = !empty($data[$puId][$sku]) ? $data[$puId][$sku] + $value['delQty'] : $value['delQty'];
            }
        }
        return $data;
    }
}
