<?php
/**
 * MRP SF报表生成及补货计算
 */


namespace App\Services\MrpBaseData;

use App\Models\Mrp\MrpBaseSkuCore;
use App\Models\Mrp\MrpReportDaySalesCountSf;
use App\Models\Mrp\MrpReportOrdersSf;
use App\Models\Mrp\MrpReportOrigSalesdataSf;
use App\Models\Mrp\MrpReportStockCountSf;
use App\Models\Mrp\MrpResultPlanSf;
use App\Models\MrpBaseData\MrpBaseOmsSalesList;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class MrpReportSfService extends ReportService
{

    /**
     * 跑当前所有报表
     */
    public function runData()
    {
        $this->reportOrdersSfFetchBySql(); //销量统计（HS）
        $this->reportOrigSaledataSfFetchBySql(); //销量源数据（HS）
        $this->reportDaySalesCountSfFetchBySql();//平台SKU日销量统计表(HS）
        $this->reportStockCountSfFetchBySql();//库存统计（HS）
        $this->resultPlanSfFetchBySql();//自动补货建议（HS）
    }


    /**
     * Desc:销量统计（HS）
     */
    public function ReportOrdersSfFetchBySql()
    {
        $times = Carbon::now()->format('Y-m-d H:i:s');
        Log::info("ReportOrdersSfService || 开始");
        MrpReportOrdersSf::query()->truncate();
        $computeBatch = $this->getComputeBatch();
        $days = range(1, 3);
        $saleInfoByDaysSql = $this->getSaleInfoByDaysSql($days, MrpBaseSkuCore::TYPE_SF, false);
        $buildSub1 = MrpBaseSkuCore::query()->select([
            'mrp_base_sku_core.sku',
            'stock_way',
            'sales_status',
            DB::raw("ifnull(nearly1days_qty,0)  nearly1days_qty"),
            DB::raw("ifnull(nearly2days_qty,0)  nearly2days_qty"),
            DB::raw("ifnull(nearly3days_qty,0)  nearly3days_qty"),
            'fixed_stock_num',
            'buffer_stock_cycle'
        ])->leftJoinSub($saleInfoByDaysSql, 't1', function ($join) {
            $join->on('t1.sku', '=', 'mrp_base_sku_core.sku');
        })->where('mrp_base_sku_core.type', '=', DB::raw(MrpBaseSkuCore::TYPE_SF));

        $rangeDays = [55, 29, 13, 6];
        $saleInfoByDaysRangeSql = $this->getSaleInfoByDaysSql($rangeDays, MrpBaseSkuCore::TYPE_SF, true);

        $buildSub2 = MrpBaseSkuCore::query()->select([
            'mrp_base_sku_core.sku',
            DB::raw("ifnull(nearly6days_qty,0)  nearly7days_qty"),
            DB::raw("ifnull(nearly13days_qty,0)  nearly14days_qty"),
            DB::raw("ifnull(nearly29days_qty,0) nearly30days_qty"),
            DB::raw("ifnull(nearly55days_qty,0) nearly55days_qty"),
        ])->leftJoinSub($saleInfoByDaysRangeSql, 't2', function ($join) {
            $join->on('t2.sku', '=', 'mrp_base_sku_core.sku');
        })->where('mrp_base_sku_core.type', '=', DB::raw(MrpBaseSkuCore::TYPE_SF));

        $buildSub3 = DB::table(DB::raw("({$buildSub1->toSql()}) as t3"))
            ->mergeBindings($buildSub1->getQuery())
            ->select([
                't3.sku',
                't3.stock_way',
                't3.sales_status',
                't3.nearly1days_qty',
                't3.nearly2days_qty',
                't3.nearly3days_qty',
                'fixed_stock_num',
                'buffer_stock_cycle',
                DB::raw($this->getOrderTimesSql()),
                DB::raw($this->getDaySalesSql()),
            ]);


        $buildSub4 = DB::table(DB::raw("({$buildSub3->toSql()}) as t4"))
            ->mergeBindings($buildSub3)
            ->leftJoinSub($buildSub2, 't5', function ($join) {
                $join->on('t5.sku', '=', 't4.sku');
            })
            ->select(
                [
                    't4.sku',
                    'stock_way',
                    'sales_status',
                    'nearly1days_qty',
                    'nearly2days_qty',
                    'nearly3days_qty',
                    'nearly7days_qty',
                    'nearly14days_qty',
                    'nearly30days_qty',
                    'nearly55days_qty',
                    'order_times',
                    'day_sales',
                    'fixed_stock_num',
                    'buffer_stock_cycle',
                    DB::raw($this->getSalesTrendSql()),
                ]
            );


        $buildSub5 = DB::table(DB::raw("({$buildSub4->toSql()}) as t6"))
            ->mergeBindings($buildSub4)
            ->select([
                'sku',
                'stock_way',
                'sales_status',
                'nearly1days_qty',
                'nearly2days_qty',
                'nearly3days_qty',
                'nearly7days_qty',
                'nearly14days_qty',
                'nearly30days_qty',
                'nearly55days_qty',
                'order_times',
                'day_sales',
                'sales_trend',
                DB::raw($this->getOrderPointSql()),
                DB::raw("'{$computeBatch}' as compute_batch")
            ]);

        MrpReportOrdersSf::query()->insertUsing([
            'sku',
            'stock_way',
            'sales_status',
            'nearly1days_qty',
            'nearly2days_qty',
            'nearly3days_qty',
            'nearly7days_qty',
            'nearly14days_qty',
            'nearly30days_qty',
            'nearly55days_qty',
            'order_times',
            'day_sales',
            'sales_trend',
            'order_point',
            'compute_batch'
        ], $buildSub5);

        //批量更新
        $timeEnd = Carbon::now()->format('Y-m-d H:i:s');
        $timeDiff = Carbon::parse($timeEnd)->diffInMinutes($times);
        Log::info("结束 || ReportOrdersSfService 时间{$timeDiff}分");
    }


    /**
     * Desc: 获取出单次数次数
     *  倒推第N天销量有关系 倒退第一天的销量是0则为0 ，倒退第2天的销量是0则为1，倒退第3天的销量是0则为2 ，其他为3
     * @return string
     *
     */
    public function getOrderTimesSql()
    {
        return 'case
                     when ifnull(nearly1days_qty,0) = 0 then 
                          0
                     when ifnull(nearly2days_qty,0) = 0 then
                          1
                     when ifnull(nearly3days_qty,0) = 0 then
                          2
                     else        
                          3
               end order_times';
    }

    /**
     * Desc: 获取日均销量，备货方式有关（stock_way）
     *
     * @return string
     */
    public function getDaySalesSql()
    {
        return 'case
                     when stock_way <> 1 and ifnull(nearly1days_qty,0) > 0 and  ifnull(nearly2days_qty,0) > 0 and ifnull(nearly3days_qty,0) = 0 then
                          least(IFNULL(nearly1days_qty,0), IFNULL(nearly2days_qty,0))
                     else
                          least(ifnull(nearly1days_qty,0),ifnull(nearly2days_qty,0),ifnull(nearly3days_qty,0))
                 end day_sales';
    }

    /**
     * Desc:获取订购点 ，与销量趋势（sales_trend）,固定备货数量(fixed_stock_num),缓冲天数(buffer_stock_cycle),日均销量（day_sales）有关
     * @return string
     */
    public function getOrderPointSql()
    {
        return 'case
                     when sales_trend in (-1, 0, 4, 5) then
                          fixed_stock_num + buffer_stock_cycle * day_sales 
                     when sales_trend = 1 then   
                          2 * day_sales + fixed_stock_num + buffer_stock_cycle * day_sales
                     when sales_trend = 2 then  
                          5 * day_sales + fixed_stock_num + buffer_stock_cycle * day_sales         
                     else
                          4 * day_sales + fixed_stock_num + buffer_stock_cycle * day_sales         
                end order_point';
    }

    /**
     * Desc:获取销量趋势
     * 备货方式有关（stock_way），出单次数（order_times），倒推第N天销量（nearly2days_qty）
     * 销量趋势:-1趋势不明0无趋势1平稳上涨2快速上涨3暴涨4连续下滑5暴跌
     * @return string
     */
    public function getSalesTrendSql()
    {
        return 'case
                    when stock_way = 1 or order_times < 3 then
                         0
                    when nearly1days_qty / nearly2days_qty >= 1.5 and nearly2days_qty / nearly3days_qty >= 1 and nearly2days_qty > 10 then
                         3
                    when nearly1days_qty / nearly2days_qty >= 1.2 and nearly2days_qty / nearly3days_qty >= 1 then
                         2
                    when nearly1days_qty / nearly2days_qty >= 1 and nearly1days_qty / nearly2days_qty < 1.2 and nearly2days_qty / nearly3days_qty >= 1 then
                         1
                    when nearly1days_qty / nearly2days_qty < 0.5 and  nearly2days_qty / nearly3days_qty < 1 and nearly2days_qty > 10 then
                         5
                    when nearly1days_qty / nearly2days_qty < 1 and nearly2days_qty / nearly3days_qty < 1 then
                         4
                    else
                        -1
               end sales_trend';
    }


    /**
     * Desc:销量源数据（HS）
     */
    public function reportOrigSaledataSfFetchBySql()
    {
        $times = Carbon::now()->format('Y-m-d H:i:s');
        Log::info("ReportOrigSaledataSfService || 开始");
        MrpReportOrigSalesdataSf::query()->truncate();
        $computeBatch = $this->getComputeBatch();
        $columns = [
            'package_code',
            'mrp_base_oms_sales_lists.sku as sku',
            'item_count',
            'warehouseid',
            DB::raw("case when warehouseid=103 then '国内三号仓'  when warehouseid=106 then '国内六号仓' else  '未知'  end  as warehouse"),
            DB::raw("ifnull(mrp_base_platform_lists.platform_cn_name,'') as platform"),
            'payment_date',
            DB::raw("'{$computeBatch}' as compute_batch")
        ];
        $build = MrpBaseOmsSalesList::query()
            ->leftJoin(
                'mrp_base_platform_lists',
                'mrp_base_platform_lists.platform_code',
                '=',
                'mrp_base_oms_sales_lists.platform_code'
            )
//            ->whereIn('mrp_base_oms_sales_lists.platform_code', OmsService::independentMap)
            ->whereNotIn('order_status', array_keys(self::omsStatusMap))
            ->select($columns);
        MrpReportOrigSalesdataSf::query()->insertUsing([
            'package_code',
            'sku',
            'item_count',
            'warehouseid',
            'warehouse',
            'platform',
            'payment_date',
            'compute_batch'
        ], $build);
        //批量更新
        $timeEnd = Carbon::now()->format('Y-m-d H:i:s');
        $timeDiff = Carbon::parse($timeEnd)->diffInMinutes($times);
        Log::info("结束 || ReportOrigSaledataSfService 时间{$timeDiff}分");
    }


    /**
     * Desc:平台SKU日销量统计表(HS）
     */
    public function reportDaySalesCountSfFetchBySql()
    {
        $times = Carbon::now()->format('Y-m-d H:i:s');
        Log::info("ReportDaySalesCountSfService || 开始");
        MrpReportDaySalesCountSf::query()->truncate();
        $computeBatch = $this->getComputeBatch();
        $ranges = range(0, 29);
        $buildSub1 = $this->getSaleInfoByDaysSql(
            $ranges,
            MrpBaseSkuCore::TYPE_SF
        )->addSelect(DB::raw("'{$computeBatch}' as compute_batch"));

        MrpReportDaySalesCountSf::query()->insertUsing([
            'sku',
            'old_day_sales_1',
            'old_day_sales_2',
            'old_day_sales_3',
            'old_day_sales_4',
            'old_day_sales_5',
            'old_day_sales_6',
            'old_day_sales_7',
            'old_day_sales_8',
            'old_day_sales_9',
            'old_day_sales_10',
            'old_day_sales_11',
            'old_day_sales_12',
            'old_day_sales_13',
            'old_day_sales_14',
            'old_day_sales_15',
            'old_day_sales_16',
            'old_day_sales_17',
            'old_day_sales_18',
            'old_day_sales_19',
            'old_day_sales_20',
            'old_day_sales_21',
            'old_day_sales_22',
            'old_day_sales_23',
            'old_day_sales_24',
            'old_day_sales_25',
            'old_day_sales_26',
            'old_day_sales_27',
            'old_day_sales_28',
            'old_day_sales_29',
            'old_day_sales_30',
            'compute_batch'
        ], $buildSub1);


        $timeEnd = Carbon::now()->format('Y-m-d H:i:s');
        $timeDiff = Carbon::parse($timeEnd)->diffInMinutes($times);
        Log::info("结束 || ReportDaySalesCountSfService 时间{$timeDiff}分");
    }


    /**
     * Desc:库存统计HS
     * 可用库存（available_stock_num） = 实际库存（actual_stock_num）- wms占用库存（newwms_use_num）- 订单占用（occupy_stock_num）
     * 总库存（total_stock_num）= 未建单PR数（no_order_pr_num）+   未打印库采购单数（no_print_num）+ 采购在途（purchase_on_way_num） + 可用库存（available_stock_num）
     * newwms_use_num 从wms来  occupy_stock_num从oms来
     */
    public function reportStockCountSfFetchBySql()
    {
        $times = Carbon::now()->format('Y-m-d H:i:s');
        Log::info("MrpReportStockCountSfService || 开始");
        MrpReportStockCountSf::query()->truncate();
        $computeBatch = $this->getComputeBatch();
        $build = MrpBaseSkuCore::query()
            ->select([
                'mrp_base_sku_core.sku',
                'mrp_base_sku_core.stock_way',
                'mrp_base_sku_core.sales_status',
                DB::raw('ifnull(order_times ,0)  order_times'),
                DB::raw('ifnull(sum(no_order_pr_num + no_print_num),0) pr_count'),
                DB::raw('ifnull(sum(no_order_pr_num),0) no_order_pr_num'),
                DB::raw('ifnull(sum(no_print_num),0)  no_print_num'),
                DB::raw('ifnull(sum(purchase_on_way_num),0)  purchase_on_way_num'),
                DB::raw('ifnull(sum(actual_stock_num - newwms_use_num - occupy_stock_num),0)  available_stock_num'),
                DB::raw('ifnull(sum(actual_stock_num),0)  actual_stock_num'),
                DB::raw('ifnull(sum(newwms_use_num),0)  newwms_use_num'),
                DB::raw('ifnull(sum(occupy_stock_num),0)  occupy_stock_num'),
                DB::raw('ifnull(sum(no_order_pr_num + no_print_num + purchase_on_way_num + actual_stock_num - newwms_use_num - occupy_stock_num),0)  total_stock_num'),
                DB::raw("'{$computeBatch}' as compute_batch")
            ])->leftjoin('mrp_base_sku_stock_lists', function ($join) {
                $join->on('mrp_base_sku_stock_lists.sku', '=', 'mrp_base_sku_core.sku');
            })
            ->leftjoin('mrp_report_orders_sf', function ($join) {
                $join->on('mrp_report_orders_sf.sku', '=', 'mrp_base_sku_core.sku');
            })
            ->where('type', MrpBaseSkuCore::TYPE_SF)->groupBy('mrp_base_sku_core.sku');

        MrpReportStockCountSf::query()->insertUsing([
            'sku',
            'stock_way',
            'sales_status',
            'order_times',
            'pr_count',
            'no_order_pr_num',
            'no_print_num',
            'purchase_on_way_num',
            'available_stock_num',
            'actual_stock_num',
            'newwms_use_num',
            'occupy_stock_num',
            'total_stock_num',
            'compute_batch',
        ], $build);

        $timeEnd = Carbon::now()->format('Y-m-d H:i:s');
        $timeDiff = Carbon::parse($timeEnd)->diffInMinutes($times);
        Log::info("结束 || fetchMrpReportStockCountSf 时间{$timeDiff}分");
    }


    public function resultPlanSfFetchBySql()
    {
        $times = Carbon::now()->format('Y-m-d H:i:s');
        Log::info("ResultPlanSfService || 开始");
        MrpResultPlanSf::query()
            ->where('confirm_status', '!=', MrpResultPlanSf::CONFIRM_STATUS_SYSTEM_CANCELLATION)
            ->update(['confirm_status' => MrpResultPlanSf::CONFIRM_STATUS_SYSTEM_CANCELLATION]);
        $computeBatch = $this->getComputeBatch();
        $build = MrpBaseSkuCore::query()->select([
            'mrp_base_sku_core.sku',
            'mrp_base_sku_core.stock_way',
            'mrp_base_sku_core.sales_status',
            DB::raw("ifnull(main_warehouseid,'') as  warehouseid"),
            'fixed_stock_num',
            'buffer_stock_cycle',
            'supply_cycle',
            'mrp_report_orders_sf.order_times',
            'mrp_report_orders_sf.day_sales',
            'nearly1days_qty',
            'nearly2days_qty',
            'nearly3days_qty',
            'sales_trend',
            'pr_count',
            'purchase_on_way_num',
            'available_stock_num',
            'actual_stock_num',
            'newwms_use_num',
            'occupy_stock_num',
            'total_stock_num',
            'order_point',
            DB::raw($this->getReplenishmentNumSql()),
            DB::raw($this->getRequestDateSql()),
            DB::raw('1 as  confirm_status'),
            'sku_mark',
            DB::raw($this->getPriceSql()),
            DB::raw("ifnull(planner_nick,'') as  planner_nick"),
            DB::raw("'{$computeBatch}' as compute_batch")
        ])->where('type', MrpBaseSkuCore::TYPE_SF)
            ->join('mrp_report_stock_count_sf', function ($join) {
                $join->on('mrp_report_stock_count_sf.sku', '=', 'mrp_base_sku_core.sku');
            })
            ->join('mrp_report_orders_sf', function ($join) {
                $join->on('mrp_report_orders_sf.sku', '=', 'mrp_base_sku_core.sku');
            })
            ->join('mrp_base_sku_info_lists', function ($join) {
                $join->on('mrp_base_sku_info_lists.sku', '=', 'mrp_base_sku_core.sku');
            })->where('main_warehouseid', '>', 0);

        //

        $buildSub1 = DB::table(DB::raw("({$build->toSql()}) as t1"))
            ->mergeBindings($build->getQuery())
            ->where('replenishment_num', '>', 0)
            ->select([
                'sku',
                'stock_way',
                'sales_status',
                'warehouseid',
                'fixed_stock_num',
                'buffer_stock_cycle',
                'supply_cycle',
                'order_times',
                'day_sales',
                'nearly1days_qty',
                'nearly2days_qty',
                'nearly3days_qty',
                'sales_trend',
                'pr_count',
                'purchase_on_way_num',
                'available_stock_num',
                'actual_stock_num',
                'newwms_use_num',
                'occupy_stock_num',
                'total_stock_num',
                'order_point',
                'replenishment_num',
                'request_date',
                'confirm_status',
                'sku_mark',
                'price',
                'planner_nick',
                'compute_batch'
            ]);

        MrpResultPlanSf::query()->insertUsing([
            'sku',
            'stock_way',
            'sales_status',
            'warehouseid',
            'fixed_stock_num',
            'buffer_stock_cycle',
            'supply_cycle',
            'order_times',
            'day_sales',
            'nearly1days_qty',
            'nearly2days_qty',
            'nearly3days_qty',
            'sales_trend',
            'pr_count',
            'purchase_on_way_num',
            'available_stock_num',
            'actual_stock_num',
            'newwms_use_num',
            'occupy_stock_num',
            'total_stock_num',
            'order_point',
            'replenishment_num',
            'request_date',
            'confirm_status',
            'sku_mark',
            'price',
            'planner_nick',
            'compute_batch'
        ], $buildSub1);

        $timeEnd = Carbon::now()->format('Y-m-d H:i:s');
        $timeDiff = Carbon::parse($timeEnd)->diffInMinutes($times);
        Log::info("结束 || ResultPlanSfService 时间{$timeDiff}分");
    }

    /**
     * Desc:获取需求日期
     * 可用库存（available_stock_num），日均销量（day_sales）
     * @return string
     */
    public function getRequestDateSql()
    {
        return 'case
                    when available_stock_num <0  then 
                         curdate()	
                    when day_sales = 0  then 
                         curdate()	
                    else
                        date_add(curdate(),interval available_stock_num / day_sales day)	
               end request_date';
    }

    /**
     * Desc:获取单价
     * 一次找到 最近一次入库价格（last_war_price），供应商最小采购价（supplier_min_price），SKU参考价（price）
     * @return string
     */
    public function getPriceSql()
    {
        return 'case
                    when last_war_price >0  then 
                         last_war_price
                    when supplier_min_price >0  then 
                         supplier_min_price
                    when price >0  then 
                         price     
                    else
                        0
               end price';
    }

    /**
     *    补货数
     *  总库存数（total_stock_num），订购点（order_point），备货方式（stock_way） 出单次数（order_times），日均销量（day_sales）
     *    备货方式：1：出单备货 2：备货销
     */
    public function getReplenishmentNumSql()
    {
        return 'ceil(
                case when total_stock_num < order_point then   			
                          case when mrp_base_sku_core.stock_way = 1 then order_point - total_stock_num 			
                               else 			
                                    case when mrp_report_orders_sf.order_times in (0, 1) then order_point - total_stock_num 			
                                         when mrp_report_orders_sf.order_times = 2 then order_point - total_stock_num + day_sales * 2 			
                                         when mrp_report_orders_sf.order_times = 3 then 			
                                            case 			
                                                 when sales_trend in (1, 2, 3) then  day_sales * supply_cycle + order_point - total_stock_num 			
                                                 when sales_trend = 4 then order_point - total_stock_num + day_sales * 2 			
                                                 when sales_trend = 5 then order_point - total_stock_num + day_sales * 0.5 			
                                                 else order_point - total_stock_num + day_sales 			
                                            end 			
                                     end 			
                          end			
                else 
                     0 
                end
              ) replenishment_num';
    }
}
