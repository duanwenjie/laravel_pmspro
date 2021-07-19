<?php
/**
 * Description
 * User: dwj
 * Date: 2021/5/4
 * Time: 10:34 上午
 */

namespace App\Services\MrpBaseData;

use App\Models\MrpBaseData\MrpBaseOmsSalesList;
use App\Tools\Formater;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class OmsService
{
    public const omsDb1 = [
        'oms_eb',
        'oms_wh',
        'oms_ya',
        'oms_sm',
        'oms_mm',
        'oms_jo',
        'oms_se',
        'oms_bw',
        'oms_os',
        'oms_wm',
        'oms_cp',
        'oms_pm',
        'oms_dd',
        'oms_ne',
        'oms_kp',
        'oms_vo',
    ];
    public const omsDb2 = [
        'oms_sf',
        'oms_lz',
        'oms_jm',
        'oms_cd',
        'oms_rt',
        'oms_jd',
        'oms_ln',
        'oms_xx',
        'oms_dy',
        'oms_ma',
        'oms_da',
        'oms_xp',
        'oms_rd',
        'oms_sy',
        'oms_sl',
        'oms_sz',
        'oms_fn',
        'oms_tj',
        'oms_fd',
    ];
    public const type = [
        'oms_eb' => 'EBAY',
        'oms_wh' => 'WISH',
        'oms_ya' => 'AMAZON',
        'oms_sm' => 'SMT',
        'oms_mm' => 'MYMALL',
        'oms_jo' => 'JOOM',
        'oms_se' => 'SHOPEE',
        'oms_sf' => 'SHOPIFY',
        'oms_lz' => 'LAZADA',
        'oms_jm' => 'JUMIA',
        'oms_cd' => 'CD',
        'oms_rt' => 'rakuten',
        'oms_jd' => 'JD',
        'oms_ln' => 'LINIO',
        'oms_xx' => 'B2B',
        'oms_dy' => 'douyin',
        'oms_ma' => 'Mercadolibre',
        'oms_da' => 'new_daraz',
        'oms_bw' => 'B2W',
        'oms_os' => 'overstock',
        'oms_wm' => 'WalMart',
        'oms_cp' => 'coupang',
        'oms_pm' => 'priceminister',
        'oms_xp' => 'xshoppy',
        'oms_rd' => 'real.de',
        'oms_sy' => 'shopyy',
        'oms_sl' => 'shopline',
        'oms_sz' => 'shoplazza',
        'oms_kp' => 'Kaspi',
        'oms_dd' => '拼多多',
        'oms_ne' => 'newegg',
        'oms_tj' => 'tuijian',
        'oms_fd' => 'Fordeal',
        'oms_vo' => 'VOVA',
    ];

    //独立站的 平台编码
    public const independentMap = [
        'SF',
        'XP',
        'SY',
        'SL',
        'SZ',
    ];

    // OMS仓库
    public const omsWarehouseMap = [
        'CCN003' => '103', // 国内三号仓
        'CCN006' => '106'  // 国内六号仓
    ];

    // 仓库和中文名转换
    public const warehouseMap = [
        '103' => '国内三号仓',
        '106' => '国内六号仓'
    ];


    // OMS 已撤单状态
    public const omsCancelStatusMap = [
        9  => '平台撤单',
        15 => '已删除',
    ];

    // OMS 非总未发状态
    public const omsNoOccupyStatusMap = [
        1  => '已推送',
        3  => '已发货',
        4  => '已取消',
        5  => '已撤单',
        6  => '待审核',
        7  => '未发货且已推送',
        9  => '平台撤单',
        15 => '已删除',
        18 => '已退件',
    ];

    /**
     * 根据支付时间抓取特定范围特定订单状态(排除self::omsCancelStatusMap)的包裹，排除掉被oms标记的特殊包裹（package_info_extend4.is_special=1）
     * self::omsMap 下的平台 拉取支付时间是55天内的订单 ，其余拉取30天
     * OMS 仓库存储的仓库是CCN003 需要转成我们系统需要的编码
     * 最终写入mrp_base_oms_sales_lists（OMS销量抓取存储表）
     * @author dwj
     */
    public static function syncMrpBaseOmsSalesData()
    {
        // 遍历OMS数据库抓取数据
        $times = Carbon::now()->format('Y-m-d H:i:s');
        Log::info("同步OMS销量数据 || 开始");
        $allDb = array_merge(self::omsDb1, self::omsDb2);
        //如果是非正式环境只抓取oms_eb和oms_sf两个库
        if (config('app.env') != 'production') {
            $allDb = ['oms_eb', 'oms_sf'];
        }
        $num = 0;
        foreach ($allDb as $dbName) {
            //独立站的数据链接编码
            $omsIndependentMap = array_map(function ($v) {
                return 'oms_'.strtolower($v);
            }, self::independentMap);
            if (in_array($dbName, $omsIndependentMap)) {
                $start = Carbon::now()->subDays(56)->format('Y-m-d');
                $days = 56;
            } else {
                $start = Carbon::now()->subDays(31)->format('Y-m-d');
                $days = 31;
            }
            for ($i = 1; $i <= $days; $i++) {
                if (Carbon::parse($start)->timestamp > Carbon::now()->timestamp) {
                    break;
                }
                $end = Carbon::parse($start)->addDay()->format('Y-m-d');
                $db = in_array($dbName, self::omsDb1) ? 'oms' : 'oms_sf';
                $start = $end;
                $data = DB::connection($db)->table("{$dbName}.package_info as b")
                    ->leftJoin("{$dbName}.package_product_info as c", 'b.package_code', '=', 'c.package_code')
                    ->whereIn('b.warehouse_code', array_keys(self::omsWarehouseMap))
                    ->whereBetween('b.payment_date', [$start.' 00:00:00', $end.' 23:59:59'])
                    ->whereNotIn('b.package_status', array_keys(self::omsCancelStatusMap))
                    ->whereNotNull('c.sku_code')
                    ->whereNotExists(function ($query) {
                        $query->select(DB::raw(1))->from('package_info_extend4')->whereRaw('package_code = b.package_code AND is_special = 1');
                    })
                    ->select([
                        'b.package_code',
                        'b.order_source_id',
                        'b.payment_date',
                        DB::raw('b.warehouse_code as warehouseid'),
                        DB::raw("upper(replace(trim(c.sku_code),' ','')) sku"),
                        DB::raw('b.orders_belong_account sales_account'),
                        DB::raw('c.count item_count'),
                        DB::raw('b.package_status order_status'),
                        DB::raw('b.package_amount total_amount')
                    ])
                    ->get()
                    ->toArray();
                $groupData = [];
                foreach ($data as $v) {
                    $key = $v->package_code.'_'.$v->sku;
                    if (!empty($groupData[$key])) {
                        $v->item_count += $groupData[$key]->item_count;
                    }
                    $groupData[$key] = $v;
                }
                $data = array_values($groupData);
                $num += count($data);
                unset($groupData);
                $data = array_chunk($data, 3000);
                foreach ($data as $item) {
                    $temp = [];
                    foreach ($item as $value) {
                        $warehouseId = self::omsWarehouseMap[$value->warehouseid] ?? 0;
                        $temp[] = [
                            'sku'             => $value->sku,
                            'package_code'    => $value->package_code,// 包裹单号
                            'platform_code'   => substr($value->package_code, 0, 2),// 平台代码 (截取包裹单号前两位)
                            'warehouseid'     => $warehouseId,
                            'sales_account'   => $value->sales_account,
                            'payment_date'    => $value->payment_date,
                            'item_count'      => $value->item_count,
                            'order_status'    => $value->order_status,
                            'total_amount'    => $value->total_amount,
                            'order_source_id' => $value->order_source_id,// 平台订单号
                        ];
                    }
                    $sql = Formater::sqlInsertAll('mrp_base_oms_sales_lists', $temp);
                    DB::insert($sql);
                    //MrpBaseOmsSalesList::query()->insert($temp);
                    //Log::info("执行完{$num}条");
                }
            }
        }
        $timeEnd = Carbon::now()->format('Y-m-d H:i:s');
        $timeDiff = Carbon::parse($timeEnd)->diffInMinutes($times);
        Log::info("结束 || 获取OMS销量:总花费时间{$timeDiff}分;执行完{$num}条");
    }


    /**
     * 获取OMS总未发数据
     * OMS (总未发数量) occupy_stock_num 查询 mrp_base_oms_sales_lists 状态不是self::omsNoOccupyStatusMap范围内的订单
     * @author dwj
     */
    public static function getOccupyStockNum()
    {
        $times = Carbon::now()->format('Y-m-d H:i:s');
        Log::info("OMS (总未发数量) || 开始");
        $i = 0;
        $data = MrpBaseOmsSalesList::query()
            ->whereNotIn('order_status', array_keys(self::omsNoOccupyStatusMap))
            ->select([
                'sku',
                'warehouseid',
                DB::raw('sum(item_count) as occupy_stock_num')
            ])
            ->groupBy('sku')
            ->get()
            ->toArray();

        $data = array_chunk($data, 3000);
        foreach ($data as $items) {
            $i += count($items);
            $temp = [];
            foreach ($items as $item) {
                $warehouseId = $item['warehouseid'] ?? 0;
                $sku = $item['sku'] ?? '';
                if (empty($sku)) { // 排除脏数据
                    continue;
                }
                $temp[] = [
                    'sku'              => $sku,
                    'warehouseid'      => $warehouseId,
                    'occupy_stock_num' => $item['occupy_stock_num'],
                ];
            }
            $sql = Formater::sqlInsertAll('mrp_base_sku_stock_lists', $temp, ['occupy_stock_num']);
            $sql && DB::insert($sql);
            //Log::info("执行完{$i}条");
        }
        unset($data);

        $timeEnd = Carbon::now()->format('Y-m-d H:i:s');
        $timeDiff = Carbon::parse($timeEnd)->diffInMinutes($times);
        Log::info("结束 || OMS总未发数量::总花费时间{$timeDiff}分,执行完{$i}条");
    }


    /**
     * 每半小时全量同步OMS从库的总未发数据到PMSPRO
     * @author dwj
     */
    public static function getOmsOccupyStockNum()
    {
        // 遍历OMS数据库抓取数据
        $times = Carbon::now()->format('Y-m-d H:i:s');
        Log::info("同步OMS从库总未发数据 || 开始");
        $allDb = array_merge(self::omsDb1, self::omsDb2);
        // 如果是非正式环境只抓取oms_eb和oms_sf两个库
        if (config('app.env') != 'production') {
            $allDb = ['oms_eb', 'oms_sf'];
        }

        $num = 0;
        $groupData = [];
        foreach ($allDb as $dbName) {
            //独立站的数据链接编码
            $omsIndependentMap = array_map(function ($v) {
                return 'oms_'.strtolower($v);
            }, self::independentMap);
            if (in_array($dbName, $omsIndependentMap)) {
                $start = Carbon::now()->subDays(56)->format('Y-m-d');
                $days = 56;
            } else {
                $start = Carbon::now()->subDays(31)->format('Y-m-d');
                $days = 31;
            }
            for ($i = 1; $i <= $days; $i++) {
                if (Carbon::parse($start)->timestamp > Carbon::now()->timestamp) {
                    break;
                }
                $end = Carbon::parse($start)->addDay()->format('Y-m-d');
                $db = in_array($dbName, self::omsDb1) ? 'oms_ck' : 'oms_sf_ck'; // 取OMS从库数据库
                $start = $end;
                $data = DB::connection($db)->table("{$dbName}.package_info as b")
                    ->leftJoin("{$dbName}.package_product_info as c", 'b.package_code', '=', 'c.package_code')
                    ->whereIn('b.warehouse_code', array_keys(self::omsWarehouseMap))
                    ->whereBetween('b.payment_date', [$start.' 00:00:00', $end.' 23:59:59'])
                    ->whereNotIn('b.package_status', array_keys(self::omsNoOccupyStatusMap)) // OMS非总未发状态
                    ->whereNotNull('c.sku_code')
                    ->whereNotExists(function ($query) {
                        $query->select(DB::raw(1))->from('package_info_extend4')->whereRaw('package_code = b.package_code AND is_special = 1');
                    })
                    ->select([
                        'b.payment_date',
                        DB::raw('b.warehouse_code as warehouseid'),
                        DB::raw("upper(replace(trim(c.sku_code),' ','')) sku"),
                        //DB::raw('b.orders_belong_account sales_account'),
                        DB::raw('c.count item_count'),
                    ])
                    ->get()
                    ->toArray();

                foreach ($data as $v) {
                    $warehouseId = self::omsWarehouseMap[$v->warehouseid] ?? 0;
                    $key = $warehouseId.'_'.$v->sku; // 仓库+SKU唯一键
                    if (!empty($groupData[$key])) {
                        $v->item_count += $groupData[$key]->item_count;
                    }
                    $groupData[$key] = $v;
                }
            }
        }

        $data = array_values($groupData);
        $data = array_chunk($data, 3000);
        unset($groupData);

        foreach ($data as $item) {
            $num += count($item);
            $temp = [];
            foreach ($item as $value) {
                $warehouseId = self::omsWarehouseMap[$value->warehouseid] ?? 0;
                $temp[] = [
                    'sku'              => $value->sku,
                    'warehouseid'      => $warehouseId,
                    //'sales_account'   => $value->sales_account,
                    'occupy_stock_num' => $value->item_count, // OMS总未发数量
                ];
            }
            $sql = Formater::sqlInsertAll('base_stock_oms_pms_lists', $temp, ['occupy_stock_num']);
            DB::insert($sql);
            //Log::info("执行完{$num}条");
        }

        $timeEnd = Carbon::now()->format('Y-m-d H:i:s');
        $timeDiff = Carbon::parse($timeEnd)->diffInMinutes($times);
        Log::info("结束 || 同步OMS从库总未发数据:总花费时间{$timeDiff}分;执行完{$num}条");
    }
}
