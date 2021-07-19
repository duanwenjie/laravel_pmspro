<?php
/**
 * mrp模块其他类报表
 * User: jiangshilin(jiangshilin@youkeshu.com)
 * Date: 2021/5/22
 * Time: 17:34
 */


namespace App\Services\MrpBaseData;

use App\Models\Mrp\MrpBaseSkuCore;
use App\Models\Mrp\MrpReportDaySalesCount;
use App\Models\Mrp\MrpReportOosOrdersD;
use App\Models\Mrp\MrpReportOosOrdersDAllV2;
use App\Models\Mrp\MrpReportOosOrdersDetailDaily;
use App\Models\Mrp\MrpReportOosOrdersDetailTotal;
use App\Models\Mrp\MrpReportOosOrdersDHisV2;
use App\Models\Mrp\MrpReportOosOrdersDV2;
use App\Models\Mrp\MrpReportOosOrdersM;
use App\Models\Mrp\MrpReportOosOrdersW;
use App\Models\Mrp\MrpReportSalesCountPlatform;
use App\Models\Mrp\MrpReportSalesCountPlatformAll;
use App\Models\Mrp\MrpReportSalesCountSku;
use App\Models\Mrp\MrpReportSalesCountSkuDetail;
use App\Models\MrpBaseData\MrpBaseOmsSalesList;
use App\Models\MrpBaseData\MrpBaseSkuInfoList;
use App\Models\MrpBaseData\MrpTmpOmsSalesLists;
use App\Tools\Formater;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class MrpOtherReportService extends ReportService
{
    //sku销量明细排除状态
    const NOT_DETAIL_SALES_STATUS = [
        5  => '已撤单',
        7  => '未发货且已推送',
        9  => '平台撤单',
        15 => '已删除'
    ];

    public static $data = [];

    /**
     * 跑mrp其他报表
     */
    public function runData()
    {
        $qstart = Carbon::now()->format('Y-m-d H:i:s');
        $this->mrpReportSalesCountPlatform();//MRP(国内)-》平台+SKU销量统计

        $this->mrpTmpOmsSalesLists();//MRP(国内)-》OMS销量包裹状态统计
        $this->mrpReportOosOrdersD();//MRP(国内)-》日缺货率统计报表
        $this->mrpReportOosOrdersW();//MRP(国内)-》周缺货率统计报表
        $this->mrpReportOosOrdersM();//MRP(国内)-》月缺货率统计报表

        //有一些报表只需要上午跑
        if (date('H') <= 12) {
            $this->mrpReportOosOrdersDHisV2();//MRP(国内)-》历史每日缺货占比统计表
            $this->mrpReportOosOrdersDetailDaily();//MRP(国内)-》每日缺货订单明细
            $this->mrpReportDaySalesCount();//MRP(国内)-》sku日均销量统计报表
            $this->mrpReportSalesCountSkuDetail();//MRP(国内)-》MRP V3-》销量-SKU明细
            $this->mrpReportSalesCountSku();//MRP(国内)-》MRP V3-》销量-SKU统计
        }
        $this->mrpReportOosOrdersDetailTotal();//MRP(国内)-》总缺货订单明细
        $this->mrpReportOosOrdersDv2();//MRP(国内)-》每日最新缺货占比统计报表
        $this->mrpReportOosOrdersDAllV2();//MRP(国内)-》撤单和缺货订单日统计
        return ['start' => $qstart, 'end' => Carbon::now()->format('Y-m-d H:i:s')];
    }

    /**
     * MRP(国内)-》平台+SKU销量统计    mrp_report_sales_count_platform
     * mrp_report_sales_count_platform_all 逻辑剔除的账号都已不出单所以插入相同数据
     */
    public function mrpReportSalesCountPlatform()
    {
        MrpReportSalesCountPlatform::query()->truncate();
        MrpReportSalesCountPlatformAll::query()->truncate();
        $oldDay27 = Carbon::now()->subDays(27)->format('Y-m-d');
        $search = MrpBaseOmsSalesList::query()->from('mrp_base_oms_sales_lists', 's')
            ->select([
                's.sku',
                's.platform_code',
                DB::raw('sum(if(s.payment_date>=date_sub(curdate(),interval 6 day),s.item_count,0)) day_sales_7'),
                DB::raw('sum(if(s.payment_date>=date_sub(curdate(),interval 13 day),s.item_count,0)) day_sales_14'),
                DB::raw('sum(s.item_count) day_sales_28'),
                DB::raw('0 total_sales')//进销存此列都为0，与进销存暂时保持一致
            ])
            /*->leftJoin('mrp_base_sku_stock_lists as o', function ($join) {
                $join->on('s.sku', '=', 'o.sku')->where('o.warehouseid','103');
            })*/
            ->whereNotIn('s.order_status',array_keys(MrpReportV3Service::NOT_SALES_STATUS))
            ->where('s.payment_date', '>=', $oldDay27)
            ->groupBy('s.sku')
            ->groupBy('s.platform_code');
        DB::table('mrp_report_sales_count_platform_all')->insertUsing([
            'sku',
            'platform_code',
            'day_sales_7',
            'day_sales_14',
            'day_sales_28',
            'total_sales'
        ], $search);
        DB::table('mrp_report_sales_count_platform')->insertUsing([
            'sku',
            'platform_code',
            'day_sales_7',
            'day_sales_14',
            'day_sales_28',
            'total_sales'
        ], $search);
    }

    /**
     * MRP(国内)-》sku日均销量统计报表    mrp_report_day_sales_count
     */
    public function mrpReportDaySalesCount()
    {
        $oldDay14 = Carbon::now()->subDays(14)->format('Y-m-d');
        $oldDay29 = Carbon::now()->subDays(29)->format('Y-m-d');
        $computeBatch = $this->getComputeBatch();
        MrpReportDaySalesCount::query()
            ->where('compute_batch', '<=', $oldDay14)
            ->orWhere('compute_batch', '=',$computeBatch)
            ->delete();
        $selectFields = ['sku', 'updated_at', DB::raw("'$computeBatch' as compute_batch")];
        $dateColumns = $this->getInsertDateColumnsByDays(range(1, 30), 'old_day_sales_');
        $selectFields = array_merge($selectFields, $dateColumns);
        $columns = ['sku', 'updated_at', 'compute_batch'];
        $columns = array_merge($columns, array_keys($dateColumns));
        $search = MrpBaseOmsSalesList::query()
            ->select($selectFields)
            ->whereNotIn('order_status',array_keys(MrpReportV3Service::NOT_SALES_STATUS))
            ->where('payment_date', '>=', $oldDay29)
            ->groupBy('sku');
        return DB::table('mrp_report_day_sales_count')->insertUsing($columns, $search);
    }

    /**
     * OMS销量包裹状态统计 mrp_tmp_oms_sales_lists 类似进销存：bigdata_ods_opr_oos_orders 表
     */
    public function mrpTmpOmsSalesLists()
    {
        MrpTmpOmsSalesLists::query()->truncate();
        // 遍历OMS数据库抓取数据
        Log::info("其他报表-同步90天OMS平台销量所有数据 || 开始");
        $allDb = array_merge(OmsService::omsDb1, OmsService::omsDb2);
        //如果是非正式环境只抓取oms_eb和oms_sf两个库
        if (config('app.env') != 'production') {
            $allDb = ['oms_eb', 'oms_sf'];
        }
        foreach ($allDb as $value) {
            $start = Carbon::now()->subDays(90)->format('Y-m-d');
            $days = 90;
            for ($i = 0; $i <= $days; $i++) {
                if (Carbon::parse($start)->timestamp > Carbon::now()->timestamp) {
                    continue;
                }
                $end = Carbon::parse($start)->addDay()->format('Y-m-d');
                $db = in_array($value, OmsService::omsDb1) ? 'oms' : 'oms_sf';
                $start = $end;
                $data = DB::connection($db)->table("{$value}.package_info as b")
                    ->leftJoin("{$value}.package_product_info as c", 'b.package_code', '=', 'c.package_code')
                    ->whereIn('b.warehouse_code', array_keys(OmsService::omsWarehouseMap))
                    ->whereBetween('b.payment_date', [$start.' 00:00:00', $end.' 23:59:59'])
                    ->whereNotNull('c.sku_code')
                    ->whereNotExists(function ($query) {
                        $query->select(DB::raw(1))->from('package_info_extend4')->whereRaw('package_code = b.package_code AND is_special = 1');
                    })
                    ->select([
                        DB::raw('left(b.payment_date,10) as payment_date'),
                        DB::raw('b.orders_belong_account sales_account'),
                        DB::raw('count(distinct b.package_code) total_orders_qty'),
                        DB::raw('sum(b.package_amount) total_orders_amount'),
                        DB::raw('b.package_status order_status')
                    ])
                    ->groupBy([
                        DB::raw('left(b.payment_date,10)'),
                        'b.package_status',
                        DB::raw('trim(lower(b.orders_belong_account))')
                    ]) // 按照日期、状态，账号进行汇总
                    ->get()
                    ->toArray();

                $data = array_chunk($data, 3000);
                foreach ($data as $item) {
                    $temp = [];
                    foreach ($item as $v) {
                        $temp[] = [
                            'payment_date'        => $v->payment_date,
                            'sales_account'       => $v->sales_account,
                            'platform_code'       => strtoupper(substr($value, 4, 2)),// 平台代码 (截取包裹单号前两位)
                            'total_orders_qty'    => $v->total_orders_qty,
                            'total_orders_amount' => $v->total_orders_amount,
                            'order_status'        => $v->order_status,
                        ];
                    }
                    $sql = Formater::sqlInsertAll('mrp_tmp_oms_sales_lists', $temp);
                    DB::insert($sql);
                }
            }
        }
    }

    /**
     * MRP(国内)-》日缺货率统计报表    (其实是撤单)mrp_report_oos_orders_d
     */
    public function mrpReportOosOrdersD()
    {
        MrpReportOosOrdersD::query()->truncate();
        $search = MrpTmpOmsSalesLists::query()
            ->select([
                DB::raw('sum(if(order_status IN (5),total_orders_qty,0)) as cancel_orders_qty'),
                DB::raw('sum(if(order_status IN (5),total_orders_amount,0)) as cancel_orders_amount'),
                DB::raw('sum(total_orders_qty) as total_orders_qty'),
                DB::raw('sum(total_orders_amount) as total_orders_amount'),
                DB::raw('sum(if(order_status IN (5),total_orders_qty,0))/sum(total_orders_qty) as cancel_orders_qty_rate'),
                DB::raw('sum(if(order_status IN (5),total_orders_amount,0))/sum(total_orders_amount) as cancel_orders_amount_rate'),
                DB::raw('left(payment_date,10) as orders_export_time'),

            ])
            ->groupBy(DB::raw('left(payment_date,10)'));
        return DB::table('mrp_report_oos_orders_d')->insertUsing([
            'cancel_orders_qty',
            'cancel_orders_amount',
            'total_orders_qty',
            'total_orders_amount',
            'cancel_orders_qty_rate',
            'cancel_orders_amount_rate',
            'orders_export_time',
        ], $search);
    }

    /**
     * MRP(国内)-》周缺货率统计报表    mrp_report_oos_orders_w
     */
    public function mrpReportOosOrdersW()
    {
        MrpReportOosOrdersW::query()->truncate();
        $search = MrpReportOosOrdersD::query()
            ->select([
                DB::raw('sum(cancel_orders_qty) cancel_orders_qty'),
                DB::raw('sum(total_orders_qty) total_orders_qty'),
                DB::raw('sum(cancel_orders_qty) / sum(total_orders_qty) cancel_orders_qty_rate'),
                DB::raw('sum(cancel_orders_amount) cancel_orders_amount'),
                DB::raw('sum(total_orders_amount) total_orders_amount'),
                DB::raw('sum(cancel_orders_amount)/sum(total_orders_amount) cancel_orders_amount_rate'),
                DB::raw('concat(year(orders_export_time),week(orders_export_time)) orders_export_week'),

            ])
            ->groupBy(DB::raw('week(orders_export_time)'));
        return DB::table('mrp_report_oos_orders_w')->insertUsing([
            'cancel_orders_qty',
            'total_orders_qty',
            'cancel_orders_qty_rate',
            'cancel_orders_amount',
            'total_orders_amount',
            'cancel_orders_amount_rate',
            'orders_export_week',
        ], $search);
    }

    /**
     * MRP(国内)-》月缺货率统计报表    mrp_report_oos_orders_m
     */
    public function mrpReportOosOrdersM()
    {
        MrpReportOosOrdersM::query()->truncate();
        $search = MrpReportOosOrdersD::query()
            ->select([
                DB::raw('sum(cancel_orders_qty) cancel_orders_qty'),
                DB::raw('sum(total_orders_qty) total_orders_qty'),
                DB::raw('sum(cancel_orders_qty) / sum(total_orders_qty) cancel_orders_qty_rate'),
                DB::raw('sum(cancel_orders_amount) cancel_orders_amount'),
                DB::raw('sum(total_orders_amount) total_orders_amount'),
                DB::raw('sum(cancel_orders_amount)/sum(total_orders_amount) cancel_orders_amount_rate'),
                DB::raw('concat(year(orders_export_time),month(orders_export_time)) orders_export_month'),

            ])
            ->groupBy(DB::raw('month(orders_export_time)'));
        return DB::table('mrp_report_oos_orders_m')->insertUsing([
            'cancel_orders_qty',
            'total_orders_qty',
            'cancel_orders_qty_rate',
            'cancel_orders_amount',
            'total_orders_amount',
            'cancel_orders_amount_rate',
            'orders_export_month',
        ], $search);
    }


    /**
     * Desc:MRP(国内)-》每日缺货订单明细    mrp_report_oos_orders_detail_daily
     * @return int
     */
    public function mrpReportOosOrdersDetailDaily()
    {
        //删除8日前的数据
        MrpReportOosOrdersDetailDaily::query()->where(
            'dw_date',
            '<',
            Carbon::now()->subDays(8)->format('Y-m-d')
        )->delete();
        //删除当天的数据，允许重复跑
        MrpReportOosOrdersDetailDaily::query()
            ->where('dw_date', '=', Carbon::now()->format('Y-m-d'))
            ->delete();
        $build = MrpBaseOmsSalesList::query()->select([
            'package_code',
            'order_status',
            DB::raw("ifnull(mrp_base_platform_lists.platform_cn_name,'') as platform"),
            'total_amount',
            'sku',
            'item_count',
            'payment_date',
            'sales_account',
            DB::raw('curdate() as dw_date')
        ])->leftJoin(
            'mrp_base_platform_lists',
            'mrp_base_platform_lists.platform_code',
            '=',
            'mrp_base_oms_sales_lists.platform_code'
        )
            ->where('order_status', '=', 11) //缺货订单
            ->where('payment_date', '>=', Carbon::now()->subDays(1)->format('Y-m-d'))
            ->where('payment_date', '<', Carbon::now()->format('Y-m-d'));

        return MrpReportOosOrdersDetailDaily::query()->insertUsing([
            'package_code',
            'order_status',
            'platform',
            'total_amount',
            'sku',
            'item_count',
            'payment_date',
            'sales_account',
            'dw_date'
        ], $build);
    }


    /**
     * MRP(国内)-》总缺货订单明细    mrp_report_oos_orders_detail_total
     */
    public function mrpReportOosOrdersDetailTotal()
    {
        MrpReportOosOrdersDetailTotal::query()->truncate();
        $build = MrpBaseOmsSalesList::query()->select([
            'package_code',
            'order_status',
            DB::raw("ifnull(mrp_base_platform_lists.platform_cn_name,'') as platform"),
            'total_amount',
            'sku',
            'item_count',
            'payment_date',
            'sales_account',
            DB::raw('curdate() as dw_date')
        ])->leftJoin(
            'mrp_base_platform_lists',
            'mrp_base_platform_lists.platform_code',
            '=',
            'mrp_base_oms_sales_lists.platform_code'
        )
            ->where('order_status', '=', 11); //缺货订单

        return MrpReportOosOrdersDetailTotal::query()->insertUsing([
            'package_code',
            'order_status',
            'platform',
            'total_amount',
            'sku',
            'item_count',
            'payment_date',
            'sales_account',
            'dw_date'
        ], $build);
    }

    /**
     * Desc:MRP(国内)-》历史每日缺货占比统计表    mrp_report_oos_orders_d_his_v2 已验证
     */
    public function mrpReportOosOrdersDHisV2()
    {

        //删除8日前的数据
        MrpReportOosOrdersDHisV2::query()->where(
            'payment_date',
            '<',
            Carbon::now()->subDays(8)->format('Y-m-d')
        )->delete();
        //删除当天的数据，允许重复跑
        MrpReportOosOrdersDHisV2::query()
            ->where('updated_at', '>=', Carbon::now()->format('Y-m-d'))
            ->delete();

        $buildSub = MrpBaseOmsSalesList::query()->select([
        ])->leftJoin(
            'mrp_base_platform_lists',
            'mrp_base_platform_lists.platform_code',
            '=',
            'mrp_base_oms_sales_lists.platform_code'
        )
            ->join('mrp_base_sku_core', function ($join) {
                $join->on('mrp_base_sku_core.sku', '=', 'mrp_base_oms_sales_lists.sku')
                    ->where('type', '=', MrpBaseSkuCore::TYPE_V3)
                    ->whereIn('sales_status', [MrpBaseSkuCore::SALES_STATUS_NEW, MrpBaseSkuCore::SALES_STATUS_ON_SALE]);
            })
            ->whereNotIn('order_status', array_keys(self::omsStatusMap))
            ->where('payment_date', '>=', Carbon::now()->subDays(1)->format('Y-m-d'))
            ->where('payment_date', '<', Carbon::now()->format('Y-m-d'))
            ->select(['package_code',DB::raw('mrp_base_oms_sales_lists.platform_code'),'platform_cn_name','order_status','payment_date','total_amount'])
            ->groupBy('package_code');

        $buildSub1 = DB::table(DB::raw("({$buildSub->toSql()}) as t1"))->mergeBindings($buildSub->getQuery())->select([
                DB::raw("ifnull(platform_cn_name,'') as platform"),
                DB::raw('sum( case when order_status in (11) then 1 else 0 end ) cancel_orders_qty'),
                DB::raw('count(1) total_orders_qty'),
                DB::raw('sum(case when order_status in (11) then total_amount else 0 end ) cancel_orders_amount'),
                DB::raw('left(payment_date, 10 ) payment_date'),
                DB::raw('sum(total_amount) total_orders_amount')
            ])->groupBy(DB::raw('left (payment_date, 10)'), 'platform_cn_name');



        $buildSub2 = DB::table(DB::raw("({$buildSub1->toSql()}) as t"))
            ->mergeBindings(($buildSub1))
            ->select([
                'platform',
                'cancel_orders_qty',
                'total_orders_qty',
                DB::raw('cancel_orders_qty/total_orders_qty as cancel_orders_qty_rate'),
                'cancel_orders_amount',
                'total_orders_amount',
                DB::raw('case
		              when ifnull(total_orders_amount , 0 ) = 0 then 0
	                                else  cancel_orders_amount / total_orders_amount
	                  end cancel_orders_amount_rate'),
                'payment_date'
            ]);


        return MrpReportOosOrdersDHisV2::query()->insertUsing([
            'platform',
            'cancel_orders_qty',
            'total_orders_qty',
            'cancel_orders_qty_rate',
            'cancel_orders_amount',
            'total_orders_amount',
            'cancel_orders_amount_rate',
            'payment_date'
        ], $buildSub2);
    }


    /**
     *
     * Desc:
     * MRP(国内)-》每日最新缺货占比统计报表    mrp_report_oos_orders_d_v2 已验证
     */
    public function mrpReportOosOrdersDv2()
    {
        MrpReportOosOrdersDV2::query()->truncate();
        $buildSub = MrpTmpOmsSalesLists::query()->where(
            'payment_date',
            '>=',
            Carbon::now()->subDays(90)->format('Y-m-d')
        )
            ->where('payment_date', '<', Carbon::now()->format('Y-m-d'))
            ->select([
                DB::raw('sum( case when order_status in (11) then total_orders_qty else 0 end ) cancel_orders_qty'),
                //缺货订单个数
                DB::raw('sum(total_orders_qty) total_orders_qty'),
                //总订单个数
                DB::raw('sum(case when order_status in (11) then total_orders_amount else 0 end ) cancel_orders_amount'),
                DB::raw('left(payment_date, 10 ) payment_date'),
                DB::raw('sum(total_orders_amount) total_orders_amount')
            ])->groupBy(DB::raw('left (payment_date, 10)'));

        $buildSub1 = DB::table(DB::raw("({$buildSub->toSql()}) as t"))
            ->mergeBindings(($buildSub->getQuery()))
            ->select([
                'cancel_orders_qty',
                'total_orders_qty',
                DB::raw('cancel_orders_qty/total_orders_qty as cancel_orders_qty_rate'),
                'cancel_orders_amount',
                'total_orders_amount',
                DB::raw('case
		              when ifnull(total_orders_amount , 0 ) = 0 then 0
	                                else  cancel_orders_amount / total_orders_amount
	                  end cancel_orders_amount_rate'),
                'payment_date'
            ]);

        return MrpReportOosOrdersDV2::query()->insertUsing([
            'cancel_orders_qty',//缺货订单个数
            'total_orders_qty',
            'cancel_orders_qty_rate',
            'cancel_orders_amount',
            'total_orders_amount',
            'cancel_orders_amount_rate',
            'payment_date'
        ], $buildSub1);
    }


    /**
     *
     * Desc:MRP(国内)-》撤单和缺货订单日统计    mrp_report_oos_orders_d_all_v2
     */
    public function mrpReportOosOrdersDAllV2()
    {
        MrpReportOosOrdersDAllV2::query()->truncate();
        $buildSub = MrpTmpOmsSalesLists::query()->select([
        ])
            ->select([
                DB::raw('sum( case when order_status in (11) then total_orders_qty else 0 end ) qh_orders_qty'),
                DB::raw('sum( case when order_status in (11) then total_orders_amount else 0 end ) qh_orders_amount'),
                DB::raw('sum( case when order_status in (5) then total_orders_qty else 0 end ) cancel_orders_qty'),
                DB::raw('sum( case when order_status in (5) then total_orders_amount else 0 end ) cancel_orders_amount'),
            ]);

        $computeBatch = $this->getComputeBatch();

        $buildSub1 = DB::table(DB::raw("({$buildSub->toSql()}) as t"))
            ->mergeBindings(($buildSub->getQuery()))
            ->select([
                'cancel_orders_qty',
                'cancel_orders_amount',
                'qh_orders_qty',
                'qh_orders_amount',
                DB::raw("'$computeBatch' as orders_export_time")
            ]);

        return MrpReportOosOrdersDAllV2::query()->insertUsing([
            'cancel_orders_qty',
            'cancel_orders_amount',
            'qh_orders_qty',
            'qh_orders_amount',
            'orders_export_time'
        ], $buildSub1);
    }

    /**
     * MRP(国内)-》MRP V3-》销量-SKU明细
     * 原表：mrp_sales_count_sku_detail
     * 新表：mrp_report_sales_count_sku_detail
     */
    public function mrpReportSalesCountSkuDetail()
    {
        MrpReportSalesCountSkuDetail::query()->truncate();
        Log::info("其他报表-同步OMS 90天SKU统计销量所有数据 || 开始");
        $allDb = array_merge(OmsService::omsDb1, OmsService::omsDb2);
        //如果是非正式环境只抓取oms_eb和oms_sf两个库
        if (config('app.env') != 'production') {
            $allDb = ['oms_eb', 'oms_sf'];
        }
        foreach ($allDb as $value) {
            $end = Carbon::now()->format('Y-m-d');
            for ($i = 1; $i <= 9; $i++) {
                $tmp_day = $i * 10;
                $start = Carbon::now()->subDays($tmp_day)->format('Y-m-d');
                $db = in_array($value, OmsService::omsDb1) ? 'oms' : 'oms_sf';
                $data = DB::connection($db)->table("{$value}.package_info as b")
                    ->leftJoin("{$value}.package_product_info as c", 'b.package_code', '=', 'c.package_code')
                    ->whereIn('b.warehouse_code', array_keys(OmsService::omsWarehouseMap))
                    ->whereNotIn('b.package_status', array_keys(self::NOT_DETAIL_SALES_STATUS))
                    ->where('b.payment_date', '>=', $start)
                    ->where('b.payment_date', '<', $end)
                    ->whereNotNull('c.sku_code')
                    ->whereNotExists(function ($query) {
                        $query->select(DB::raw(1))->from('package_info_extend4')->whereRaw('package_code = b.package_code AND is_special = 1');
                    })
                    ->select([
                        DB::raw('c.sku_code orders_sku'),
                        DB::raw('sum(if(b.payment_date>=date_sub(curdate(),interval 7 day),c.count,0)) days_pcs7'),
                        DB::raw('sum(if(b.payment_date>=date_sub(curdate(),interval 14 day),c.count,0)) days_pcs14'),
                        DB::raw('sum(if(b.payment_date>=date_sub(curdate(),interval 30 day),c.count,0)) days_pcs30'),
                        DB::raw('sum(if(b.payment_date>=date_sub(curdate(),interval 60 day),c.count,0)) days_pcs60'),
                        DB::raw('sum(if(b.payment_date>=date_sub(curdate(),interval 90 day),c.count,0)) days_pcs90'),
                        DB::raw('sum(if(b.payment_date>=date_sub(curdate(),interval 180 day),c.count,0)) days_pcs180'),
                    ])
                    ->groupBy([
                        'c.sku_code',
                    ])
                    ->get()
                    ->map(function ($value) {
                        return (array)$value;
                    });
                self::doSalesData($data);
                $end = $start;
            }
        }
        self::$data && self::$data = array_chunk(array_values(self::$data), 3000);
        foreach (self::$data as $v) {
            $skus = array_column($v, 'orders_sku');
            $status = MrpBaseSkuInfoList::query()->from('mrp_base_sku_info_lists', 'c')
                ->join('base_column_lists as b', function ($join) {
                    $join->on('c.sales_status', '=', 'b.column_key')
                        ->where('b.column_name', '=', 'sku_sales_status');
                })
                ->select(['c.sku', 'b.column_value'])
                ->whereIn('c.sku', $skus)
                ->get()->pluck('column_value', 'sku');
            foreach ($v as &$vt) {
                $vt['sales_status'] = $status[$vt['orders_sku']] ?? '';
            }
            $sql = Formater::sqlInsertAll('mrp_report_sales_count_sku_detail', $v);
            DB::insert($sql);
        }
        Log::info("其他报表-同步OMS 90天SKU统计销量所有数据 || 结束");
    }

    /**
     * MRP(国内)-》MRP V3-》销量-SKU统计
     * 原表：mrp_sales_count_sku
     * 新表：mrp_report_sales_count_sku
     */
    public function mrpReportSalesCountSku()
    {
        MrpReportSalesCountSku::query()->truncate();
        $search = MrpReportSalesCountSkuDetail::query()->select([
            DB::raw('count(distinct orders_sku) sku_count'),
            DB::raw('count(case when days_pcs7 >0 then orders_sku end) days_sku_count7'),
            DB::raw('count(case when days_pcs14 >0 then orders_sku end) days_sku_count14'),
            DB::raw('count(case when days_pcs30 >0 then orders_sku end) days_sku_count30'),
            DB::raw('count(case when days_pcs60 >0 then orders_sku end) days_sku_count60'),
            DB::raw('count(case when days_pcs90 >0 then orders_sku end) days_sku_count90'),
            DB::raw('count(case when days_pcs180 >0 then orders_sku end) days_sku_count180'),
        ]);
        return MrpReportSalesCountSku::query()->insertUsing([
            'sku_count',
            'days_sku_count7',
            'days_sku_count14',
            'days_sku_count30',
            'days_sku_count60',
            'days_sku_count90',
            'days_sku_count180'
        ], $search);
    }

    //处理销量数据
    public static function doSalesData($res)
    {
        if (empty($res)) {
            return '';
        }
        $date_arr = array('7', '14', '30', '60', '90', '180');
        foreach ($res as $value) {
            $value['orders_sku'] = trim(strtoupper($value['orders_sku']));
            if (empty($value['orders_sku'])) {
                continue;
            }
            $sku = $value['orders_sku'];
            self::$data[$sku]['orders_sku'] = $sku;
            foreach ($date_arr as $k => $v) {
                $tmp_key = 'days_pcs'.$v;
                $tmp_pcs = $value[$tmp_key];
                self::$data[$sku][$tmp_key] = empty(self::$data[$sku][$tmp_key]) ? $tmp_pcs : self::$data[$sku][$tmp_key] + $tmp_pcs;
            }
        }
    }
}
