<?php

namespace App\Tools\Client;

use App\Exceptions\InvalidRequestException;
use Illuminate\Support\Facades\Http;

class TuKuClient
{
    public static function call($uri, $param, $method = 'post')
    {
        $response = Http::asForm()->retry(
            3,
            1000
        )->timeout(20)->$method(config('host.tuku_url.'.config('app.env')).$uri, $param);
        $result = $response->json();
        if (empty($result)) {
            throw new InvalidRequestException('调用tuku失败');
        }
        return $result;
    }

    /**
     * 获取SKU图片
     * @param $skuList
     * @param $isMap
     * @return array
     * @throws InvalidRequestException
     */
    public static function getSkuPic($skuList, $isMap = false)
    {
        $tukuSku = [];
        foreach ($skuList as $sku) {
            $tukuSku[] = ['sku' => $sku, 'tuku' => 1, 'type' => 'ALL'];
        }
        $response = TuKuClient::call('data/tuku/batchQuery', ['skuList' => json_encode($tukuSku)]);
        $data = [];
        foreach ($response as $value) {
            foreach ($value as $key => $picList) {
                if ($picList) {
                    $picList = array_map(function ($item) {
                        return 'https://'.str_replace(['http://', 'https://'], '', $item);
                    }, $picList);
                    $picData = $isMap ? array_values($picList)[0] : array_values($picList);
                    $data[str_replace('-1-ALL', '', $key)] = $picData;
                }
            }
        }
        return $data;
    }
}
