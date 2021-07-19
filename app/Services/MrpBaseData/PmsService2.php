<?php
/**
 * Description
 * User: dwj
 * Date: 2021/4/30
 * Time: 6:16 下午
 */

namespace App\Services\MrpBaseData;

use App\Models\MrpBaseData\BaseStockOmsPmsList;
use App\Tools\Formater;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class PmsService2
{
    // 计划单未执行可用的（PR单状态）
    protected const prNoUse = [
        '-2' => '下单异常',
        '-1' => '异常订货',
        '2'  => '部分执行',
        '3'  => '未执行',
    ];

    // 采购已建单未打印（采购单明细状态）
    protected const poNoPrint = [
        '2' => '待采购',
        '3' => '审核中',
        '4' => '可打印',
    ];

    // 采购在途的（采购单明细状态）
    protected const poOnWay = [
        '5'  => '已打印',
        '6'  => '未完全到货',
        '7'  => '完全到货',
        '9'  => '已质检',
        '10' => '未完全入库',
    ];

    protected const statusShineMap = [
        3   => 103, // 国内三号仓
        6   => 106, // 国内6号仓
        101 => 201, // 海外中转仓
        102 => 202, // FBA中转仓
    ];

    protected const noWarehouseId = [1,2,4,5,6,7]; // 不需要统计的仓库

    protected const noPoStatus = [10,70,90,100]; // 不需要统计的采购单详情状态

    /**
     * 同步PMS数据
     * @author dwj
     */
    public static function syncMrpBaseSkuPmsData()
    {
        self::getNoOrderPrNum();  // 获取PMS未建单PR数
        self::getNoPrintNum();  // 获取PMS已建单未打印数
        self::getPurchaseOnWayNum();  // 获取PMS采购在途
    }


    /**
     * PMS 未生成PO bigdata_ODS_OPR_UN_PO_PMS (未建单PR数) 状态self::prNoUse
     * @param  int  $type  : 1:mrp_sync_base_data 定时任务调取（存储快照数据） 2：mrp_sync_oms_pms_base_data调取（存储库存数据）
     * @author  dwj
     */
    public static function getNoOrderPrNum(int $type = 1)
    {
        $times = Carbon::now()->format('Y-m-d H:i:s');
        Log::info("PMS未建单PR数 || 开始");
        $i = 0;

        $data = DB::connection('pms')->table('purchaseplan')
            ->whereIn('state', array_keys(self::prNoUse))
            ->select([
                'sku',
                DB::raw('warehouse_id as warehouseid'),
                DB::raw("sum(surplus_quantity) as no_order_pr_num")
            ])
            ->groupBy('sku','warehouse_id')
            ->get()
            ->toArray();

        $data = array_chunk($data, 3000);
        foreach ($data as $items) {
            $i += count($items);
            $temp = [];
            foreach ($items as $item) {
                $warehouseId = $item->warehouseid ?? 0;
                $temp[] = [
                    'sku'             => $item->sku,
                    'warehouseid'     => $warehouseId,
                    'no_order_pr_num' => $item->no_order_pr_num,
                ];
            }
            if ($type == 1) { // 快照
                $sql = Formater::sqlInsertAll('mrp_base_sku_stock_lists', $temp, ['no_order_pr_num']);
            }else { // 永久存储
                $sql = Formater::sqlInsertAll('base_stock_oms_pms_lists', $temp, ['no_order_pr_num']);
            }
            $sql && DB::insert($sql);
            //Log::info("执行完{$i}条");
        }
        unset($data);

        $timeEnd = Carbon::now()->format('Y-m-d H:i:s');
        $timeDiff = Carbon::parse($timeEnd)->diffInMinutes($times);
        Log::info("结束 || PMS未建单PR数:总花费时间{$timeDiff}分,执行完{$i}条");
    }


    /**
     * PMS 未生成PO bigdata_ODS_OPR_UN_PO_PMS (已建单未打印数) no_print_num
     * @param  int  $type  : 1:mrp_sync_base_data 定时任务调取（存储快照数据） 2：mrp_sync_oms_pms_base_data调取（存储库存数据）
     * @author dwj
     */
    public static function getNoPrintNum(int $type = 1)
    {
        $times = Carbon::now()->format('Y-m-d H:i:s');
        Log::info("PMS已建单未打印数 || 开始");
        $i = 0;
        $data = DB::connection('pms')->table('purchaseorder_detail')
            ->whereIn('state', array_keys(self::poNoPrint))
            ->select([
                'sku',
                DB::raw('warehouse_id as warehouseid'),
                DB::raw("sum(quantity) as no_print_num"),
            ])
            ->groupBy('sku','warehouse_id')
            ->get()
            ->toArray();

        $data = array_chunk($data,3000);
        foreach ($data as $items) {
            $i += count($items);
            $temp = [];
            foreach ($items as $item) {
                $warehouseId = $item->warehouseid ?? 0;
                $temp[] = [
                    'sku'          => $item->sku,
                    'warehouseid'  => $warehouseId,
                    'no_print_num' => $item->no_print_num,
                ];
            }
            if ($type == 1) { // 快照
                $sql = Formater::sqlInsertAll('mrp_base_sku_stock_lists', $temp, ['no_print_num']);
            }else { // 永久存储
                $sql = Formater::sqlInsertAll('base_stock_oms_pms_lists', $temp, ['no_print_num']);
            }
            $sql && DB::insert($sql);
            //Log::info("执行完{$i}条");
        }
        unset($data);

        $timeEnd = Carbon::now()->format('Y-m-d H:i:s');
        $timeDiff = Carbon::parse($timeEnd)->diffInMinutes($times);
        Log::info("结束 || PMS已建单未打印数:总花费时间{$timeDiff}分,执行完{$i}条");
    }


    /**
     * PMS 采购在途 bigdata_ODS_OPR_PO_PMS (采购在途) purchase_on_way_num
     * 规则：采购单创建时间60天内特定状态self::poOnWay的采购明细
     * @param  int  $type  : 1:mrp_sync_base_data 定时任务调取（存储快照数据） 2：mrp_sync_oms_pms_base_data调取（存储库存数据）
     * @author dwj
     */
    public static function getPurchaseOnWayNum(int $type = 1)
    {
        $times = Carbon::now()->format('Y-m-d H:i:s');
        Log::info("PMS采购在途 || 开始");
        $i = 0;
        $data = DB::connection('pms')->table('purchaseorder_detail as a')
            ->join('purchaseorder as b', 'b.id', '=', 'a.purchaseorder_id')
            ->whereIn('a.state', array_keys(self::poOnWay))
            ->whereRaw('b.create_time >= DATE_SUB(curdate(),INTERVAL 60 DAY)')
            ->select([
                'a.sku',
                DB::raw('a.warehouse_id as warehouseid'),
                DB::raw("sum(a.quantity) - sum(a.ware_quantity) as purchase_on_way_num"),
            ])
            ->groupBy('a.sku','a.warehouse_id')
            ->get()
            ->toArray();

        $data = array_chunk($data,3000);
        foreach ($data as $items) {
            $i += count($items);
            $temp = [];
            foreach ($items as $item) {
                $warehouseId = $item->warehouseid ?? 103;
                $temp[] = [
                    'sku'                 => $item->sku,
                    'warehouseid'         => $warehouseId,
                    'purchase_on_way_num' => $item->purchase_on_way_num
                ];
            }
            if ($type == 1) { // 快照
                $sql = Formater::sqlInsertAll('mrp_base_sku_stock_lists', $temp, ['purchase_on_way_num']);
            }else { // 永久存储
                $sql = Formater::sqlInsertAll('base_stock_oms_pms_lists', $temp, ['purchase_on_way_num']);
            }
            $sql && DB::insert($sql);
            //Log::info("执行完{$i}条");
        }
        unset($data);

        $timeEnd = Carbon::now()->format('Y-m-d H:i:s');
        $timeDiff = Carbon::parse($timeEnd)->diffInMinutes($times);
        Log::info("结束 || PMS采购在途:总花费时间{$timeDiff}分,执行完{$i}条");
    }

    /**
     * 获取SKU采购在途数据（PMS，FBA中转仓，海外中转仓）
     * @author dwj
     */
    public static function getPurchaseOnWayNum2()
    {
        $times = Carbon::now()->format('Y-m-d H:i:s');
        Log::info("采购在途(PMS、海外仓、FBA) || 开始");
        $i = 0;
        $date = Carbon::now()->subDays(60)->format('Y-m-d');

        $data = DB::connection('yf')->table('newerphz_purchaseorder_details as pd')
            ->join('newerphz_purchaseorders as p','p.id','=','pd.purchaseorder_id')
            ->where('p.store','0') // 0为国内仓其他为海外仓
            ->whereNotIn('p.warehouseid',self::noWarehouseId)
            ->whereNotIn('pd.status',self::noPoStatus)
            ->where('p.purchaseorder_date','>=',$date)
            ->select([
                'pd.sku',
                'p.warehouseid',
                DB::raw("SUM( pd.`quantity` - pd.`ware_quantity` ) as purchase_on_way_num")
            ])
            ->groupBy('pd.sku','pd.warehouseid')
            ->get()
            ->toArray();

        $data = array_chunk($data,3000);
        foreach ($data as $items) {
            $i += count($items);
            $temp = [];
            foreach ($items as $item) {
                $warehouseId = self::statusShineMap[$item->warehouseid] ?? 0;
                $temp[] = [
                    'sku'                 => $item->sku,
                    'warehouseid'         => $warehouseId,
                    'purchase_on_way_num' => $item->purchase_on_way_num
                ];
            }
            $sql = Formater::sqlInsertAll('base_stock_oms_pms_lists', $temp, ['purchase_on_way_num']);
            $sql && DB::insert($sql);
            //Log::info("执行完{$i}条");
        }
        unset($data);

        $timeEnd = Carbon::now()->format('Y-m-d H:i:s');
        $timeDiff = Carbon::parse($timeEnd)->diffInMinutes($times);
        Log::info("结束 || 采购在途(PMS、海外仓、FBA):总花费时间{$timeDiff}分,执行完{$i}条");
    }

    /**
     * 初始化OMS，PMS库存相关数据
     * @author dwj
     */
    public static function initPmsStockNum()
    {
        BaseStockOmsPmsList::query()->update([
            'occupy_stock_num'    => 0,
            'no_order_pr_num'     => 0,
            'no_print_num'        => 0,
            'purchase_on_way_num' => 0,
        ]);
    }
}
