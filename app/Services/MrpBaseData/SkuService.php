<?php
/**
 * Description
 * User: dwj
 * Date: 2021/5/8
 * Time: 3:32 下午
 */

namespace App\Services\MrpBaseData;

use App\Models\User;
use App\Services\Common\SkuBaseService;
use App\Tools\Formater;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class SkuService
{
    /**
     * 同步SKU基础资料基础信息
     * @author dwj
     */
    public static function syncSkuBaseInfos()
    {
        $times = Carbon::now()->format('Y-m-d H:i:s');
        Log::info("同步SKU基础资料基础信息 || 开始");
        $i = 0;
        DB::connection('sku_manage')->table('skus as A')
            ->select([
                'A.id',
                'A.sku',
                'A.spu',
                'A.sales_status',
                'A.name as cn_name',
                'A.reference_price as price'
            ])
            ->where('A.status', '=', '1')
            ->chunkById(3000, function ($items) use (&$i) {
                $i += count($items);
                $update = [];
                $updateMinPrice = [];
                $updateWarePrice = [];
                $skus = array_column($items->toArray(), 'sku');
                $skuMainWarehouse = SkuBaseService::getSkuMainWareHousesBySkus($skus);
                $skuSupplierMinPrice = SkuBaseService::getSupplierMinPriceBySkus($skus);
                $skuLastWarehousePrice = SkuBaseService::getLastWarehousePriceBySkus($skus);
                $skuPlannerInfo = SkuBaseService::getSkuPlanerBySkus($skus);
                //根据用户账号转成人事系统的姓名
                $username = array_unique(array_column($skuPlannerInfo,'name'));
                $users = User::query()->whereIn('username',$username)->pluck('nickname','username');
                foreach ($items as $item) {
                    $sku = $item->sku;
                    $username  = $skuPlannerInfo[$item->sku]->name??'';
                    $update[] = [
                        'sku'                => $sku,
                        'spu'                => $item->spu,
                        'sales_status'       => $item->sales_status,
                        'main_warehouseid'   => $skuMainWarehouse[$item->sku] ?? 103,
                        'cn_name'            => $item->cn_name,
                        'planner_account'    => $username,
                        'planner_nick'       => $users[$username] ?? '',
                        'price'              => $item->price
                    ];
                    $lastWarPrice = $skuLastWarehousePrice[$item->sku] ?? '';
                    $supplierMinPrice = $skuSupplierMinPrice[$item->sku] ?? '';
                    !empty($lastWarPrice) && $updateWarePrice[] = ['sku' => $sku,'last_war_price' => $lastWarPrice];
                    !empty($supplierMinPrice) && $updateMinPrice[] = ['sku' => $sku,'supplier_min_price' => $supplierMinPrice];
                }
                $sql = Formater::sqlInsertAll('mrp_base_sku_info_lists', $update, [
                    'spu',
                    'sales_status',
                    'main_warehouseid',
                    'cn_name',
                    'planner_account',
                    'planner_nick',
                    'price'
                ]);
                $sql && DB::insert($sql);

                $sql = Formater::sqlInsertAll('mrp_base_sku_info_lists', $updateWarePrice, ['last_war_price']);
                $sql && DB::insert($sql);

                $sql = Formater::sqlInsertAll('mrp_base_sku_info_lists', $updateMinPrice, ['supplier_min_price']);
                $sql && DB::insert($sql);

                //Log::info("执行完{$i}条");
            }, 'A.id', 'id');

        $timeEnd = Carbon::now()->format('Y-m-d H:i:s');
        $timeDiff = Carbon::parse($timeEnd)->diffInMinutes($times);
        Log::info("结束 || 同步SKU基础资料基础信息花费时间：{$timeDiff}分，执行完{$i}条");
    }
}
