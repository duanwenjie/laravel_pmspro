<?php
/**
 * Description
 * User: dwj
 * Date: 2021/5/22
 * Time: 4:00 下午
 */

namespace App\Services\Common;

use Carbon\Carbon;
use Illuminate\Support\Facades\DB;

class SkuBaseService
{
    private const IS_AVAILABLE = 1; // 状态为可用

    private const IS_DISABLE = 0; // 状态未被禁用

    private const DOMESTIC_WAREHOUSEID = [1, 2, 3, 4, 5, 6, 7]; // 国内仓编码(koko系统)

    private const TYPE = 10; // 正常采购入库(入库类型)

    /**
     * 获取SKU的指定用户
     * @param $skus
     * @param $serviceLine int 业务线 1-国内仓 2-亚马逊 3-海外仓
     * @param $buyerType int 采购员类型 1-开发员 2-采购员 3-采购主管 4-核价员 5-计划员
     * @return array
     * @author dwj
     */
    private static function getSkuUser($skus, $buyerType, $serviceLine)
    {
        if (empty($skus)) {
            return [];
        }
        return DB::connection('sku')->table('user_skus as us')
            ->leftJoin('gleez.users as u', 'us.user_id', '=', 'u.id')
            ->whereIn('us.sku', $skus)
            ->where('us.isavailable', '=', 1)
            ->where('us.serviceline', '=', $serviceLine)
            ->where('us.buyertype', '=', $buyerType)
            ->select(['us.sku', 'u.name', 'u.nick'])
            ->get()
            ->keyBy('sku')
            ->all();
    }

    /**
     * 获取SKU的开发员
     * @param  array  $skus
     * @param  int  $serviceLine
     * @return array
     * @author dwj
     */
    public static function getSkuDeveloperBySkus(array $skus, $serviceLine = 1)
    {
        return self::getSkuUser($skus, 1, $serviceLine);
    }

    /**
     * 获取SKU的采购员
     * @param  array  $skus
     * @param  int  $serviceLine
     * @return array
     * @author dwj
     */
    public static function getSkuPurchserBySkus(array $skus, $serviceLine = 1)
    {
        return self::getSkuUser($skus, 2, $serviceLine);
    }

    /**
     * 获取SKU的采购主管
     * @param  array  $skus
     * @param  int  $serviceLine
     * @return array
     * @author dwj
     */
    public static function getSkuPurchaseManagerBySkus(array $skus, $serviceLine = 1)
    {
        return self::getSkuUser($skus, 3, $serviceLine);
    }

    /**
     * 获取SKU的计划员
     * @param  array  $skus
     * @param  int  $serviceLine
     * @return array
     * @author dwj
     */
    public static function getSkuPlanerBySkus(array $skus, $serviceLine = 1)
    {
        return self::getSkuUser($skus, 5, $serviceLine);
    }

    /**
     * 通过SKU获取主仓库
     * @param  array  $skus
     * @return array
     * @author dwj
     */
    public static function getSkuMainWareHousesBySkus(array $skus)
    {
        $res = DB::connection('hz')->table('sku_main_warehouse')
            ->whereIn('sku', $skus)
            ->select(['sku', 'main_warehouseid'])
            ->pluck('main_warehouseid', 'sku')
            ->toArray();
        foreach ($res as &$v) {
            // 转换主仓库 仓库ID
            $v = ($v == 6) ? 106 : 103;
        }
        return $res;
    }


    /**
     * 获取供应商SKU最低价
     * @param  array  $skus
     * @return array
     * @author dwj
     */
    public static function getSupplierMinPriceBySkus(array $skus)
    {
        return DB::connection('sku')->table('supplier_skus as A')
            ->leftJoin('supplier_skus_ext as B', 'A.id', '=', 'B.supplier_skus_id')
            ->leftJoin('suppliers as C', 'A.supplier_id', '=', 'C.id')
            ->select(['A.sku', DB::raw('MIN(A.price) as supplier_min_price')])
            ->where('C.isavailable', self::IS_AVAILABLE)
            ->where('A.isavailable', self::IS_AVAILABLE)
            ->where('B.is_disable', self::IS_DISABLE)
            ->whereIn('A.sku', $skus)
            ->groupBy('A.sku')
            ->pluck('supplier_min_price', 'A.sku')
            ->toArray();
    }


    /**
     * 获取SKU最近入库单价
     * 规则：取koko系统入库单表最近15天的国内仓的SKU的最新的入库单价
     * @param  array  $skus
     * @return array
     * @author dwj
     */
    public static function getLastWarehousePriceBySkus(array $skus)
    {
        $date = Carbon::now()->subDays(15)->format('Y-m-d');
        $maxWarehouseDate = DB::connection('yf')->table('newerphz_warehouseorders')
            ->where('warehouse_date', '>', $date)
            ->where('type', '=', self::TYPE)
            ->whereIn('warehouseid', self::DOMESTIC_WAREHOUSEID)
            ->whereIn('sku', $skus)
            ->select('sku', DB::raw('MAX(warehouse_date) warehouse_date'))
            ->groupBy('sku');

        return DB::connection('yf')->table('newerphz_warehouseorders as A')
            ->joinSub($maxWarehouseDate, 'maxWareDate', function ($join) {
                $join->on('A.sku', '=', 'maxWareDate.sku')->on('A.warehouse_date', '=', 'maxWareDate.warehouse_date');
            })
            ->where('A.warehouse_date', '>', $date)
            ->where('A.type', '=', self::TYPE)
            ->whereIn('A.warehouseid', self::DOMESTIC_WAREHOUSEID)
            ->whereIn('A.sku', $skus)
            ->select(['A.sku', DB::raw('A.single_price as last_war_price')])
            ->groupBy('A.sku')
            ->pluck('last_war_price', 'A.sku')
            ->toArray();
    }

    /**
     * 获取SKU的供应商
     * @param  array  $skus
     * @return array
     * @author dwj
     */
    public static function getSkuSupplierBySkus(array $skus)
    {
        $result = [];
        DB::connection('sku')->table('supplier_skus as A')
            ->leftJoin('supplier_skus_ext as B', 'A.id', '=', 'B.supplier_skus_id')
            ->leftJoin('suppliers as C', 'A.supplier_id', '=', 'C.id')
            ->select(['A.sku', 'A.supplier_id'])
            ->where('C.isavailable', self::IS_AVAILABLE)
            ->where('A.isavailable', self::IS_AVAILABLE)
            ->where('B.is_disable', self::IS_DISABLE)
            ->whereIn('A.sku', $skus)
            ->orderBy('A.supplier_id')
            ->each(function ($item) use (&$result) {
                $result[$item->sku][] = $item->supplier_id;
            });
        return $result;
    }
}
