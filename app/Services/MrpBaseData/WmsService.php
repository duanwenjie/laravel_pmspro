<?php
/**
 * Description
 * User: dwj
 * Date: 2021/5/5
 * Time: 11:43 上午
 */

namespace App\Services\MrpBaseData;

use App\Models\MrpBaseData\BaseStockOrderUseQtyList;
use App\Tools\Formater;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class WmsService
{
    /**
     * 同步WMS数据
     * @author dwj
     */
    public static function syncMrpBaseSkuWmsData()
    {
        OmsService::getOccupyStockNum();  // 获取OMS总未发数
        self::getWmsUseAndLeaveNum();  // 获取WMS占用库存、离位库存
    }


    /**
     * 查询base_stock_order_use_qty_lists表中的数，此表的数据来源于wms推送，推送接口WmsStockController下
     * 获取WMS
     * newwms_use_num（占用库存）
     * leave_num（离位库存）
     * actual_stock_num (SKU储位实际库存)
     * @author dwj
     */
    public static function getWmsUseAndLeaveNum()
    {
        $times = Carbon::now()->format('Y-m-d H:i:s');
        Log::info("获取WMS占用库存离位库存 || 开始");
        $i = 0;
        $data = BaseStockOrderUseQtyList::query()
            ->select([
                'sku',
                'warehouseid',
                'newwms_use_num',
                'leave_num',
                'sku_num',
            ])
            ->get()
            ->toArray();

        $data = array_chunk($data, 3000);
        foreach ($data as $items) {
            $i += count($items);
            $temp = [];
            foreach ($items as $item) {
                $warehouseId = $item['warehouseid'] ?? 0;
                $sku = $item['sku'] ?? '';
                if (empty($sku)) {
                    continue;
                }
                $temp[] = [
                    'sku'              => $sku,
                    'warehouseid'      => $warehouseId,
                    'newwms_use_num'   => $item['newwms_use_num'],
                    'actual_stock_num' => $item['sku_num'],
                    'leave_num'        => $item['leave_num'],
                ];
            }
            $sql = Formater::sqlInsertAll('mrp_base_sku_stock_lists', $temp, ['newwms_use_num', 'actual_stock_num', 'leave_num']);
            $sql && DB::insert($sql);
            //Log::info("执行完{$i}条");
        }
        unset($data);

        $timeEnd = Carbon::now()->format('Y-m-d H:i:s');
        $timeDiff = Carbon::parse($timeEnd)->diffInMinutes($times);
        Log::info("结束 || WMS占用库存、离位库存:总花费时间{$timeDiff}分,执行完{$i}条");
    }

    /**
     * 初始化未更新的SKU WMS的离位库存数据
     * @param $noUpSku
     * @author dwj
     */
    public static function initSkuLeaveNum($noUpSku)
    {
        $update = [];
        foreach ($noUpSku as $item) {
            $temp = explode('_', $item);
            $sku = $temp[0];
            $warehouseId = $temp[1];
            $update[] = [
                'sku'         => $sku,
                'warehouseid' => $warehouseId,
                'leave_num'   => 0,
            ];
        }
        BaseStockOrderUseQtyList::query()->upsert($update, ['sku', 'warehouseid']);
    }


    /**
     * 获取离位库存大于0的SKU数据
     * @return array
     * @author dwj
     */
    public static function getOldUpSku()
    {
        $oldUpSku = [];
        BaseStockOrderUseQtyList::query()
            ->where('leave_num', '>', '0')
            ->orderBy('sku')
            ->select(['sku', 'warehouseid'])
            ->chunk(3000, function ($items) use (&$oldUpSku) {
                foreach ($items as $item) {
                    $oldUpSku[] = $item->sku.'_'.$item->warehouseid;
                }
            });

        return $oldUpSku;
    }
}
