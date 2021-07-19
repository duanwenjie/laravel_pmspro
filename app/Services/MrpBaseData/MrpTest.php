<?php
/**
 * mrp模块测试数据专用：
 * 同步进销存数据，与进销存比对差异
 * User: jiangshilin(jiangshilin@youkeshu.com)
 * Date: 2021/6/4
 * Time: 15:33
 */


namespace App\Services\MrpBaseData;

use App\Tools\Formater;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class MrpTest
{
    protected $data;
    protected $limit = 100000;

    /**
     * 同步进销存跑mrp时的销量，备货关系表，库存数据；保持跑mrp时源数据一致
     * 方便验证进销存跑mrp的结果与pmspro跑mrp结果无差异对比
     * @return array
     */
    public function syncHzBaseData()
    {
        set_time_limit(3600);
        ini_set('memory_limit', '4096M');
        $qstart = Carbon::now()->format('Y-m-d H:i:s');
        $this->syncBaseSales();//销量
        $this->syncBaseCore();//备货关系表
        $this->syncBaseStock();//库存
        //$this->iniData();
        return ['start' => $qstart, 'end' => Carbon::now()->format('Y-m-d H:i:s')];
    }

    //同步进销存抓取号的销量
    public function syncBaseSales()
    {
        //bigdata_ODS_OPR_ORDERS_LOG to mrp_base_oms_sales_lists 销量
        $table = 'mrp_base_oms_sales_lists';
        DB::table($table)->truncate();
        for ($i = 0; $i <= 56; $i++) {
            $start = Carbon::now()->subDays($i)->format('Y-m-d');
            $end = $start.' 23:59:59';
            $this->data = DB::connection('hz')->select("
                SELECT
                        right(WAREHOUSEID,1)+100 warehouseid,
                        ERP_ORDERS_ID package_code,
                        left(ERP_ORDERS_ID,2) platform_code,
                        ORDERS_SKU sku,
                        SALES_ACCOUNT sales_account,
                        ORDERS_EXPORT_TIME payment_date,
                        SUM(ITEM_COUNT) item_count,
                        ORDERS_STATUS order_status,
                        TOTAL_AMOUNT total_amount,
                        YKSID order_source_id
                FROM bigdata_ODS_OPR_ORDERS_LOG
                WHERE ORDERS_EXPORT_TIME BETWEEN '$start' AND '$end'
                GROUP BY ERP_ORDERS_ID,ORDERS_SKU");
            $this->insertData($table);
        }
    }

    //同步备货关系表

    public function insertData($table, $update = [])
    {
        if (!empty($table) && !empty($this->data)) {
            $this->data = array_map(function ($item) {
                return (array)$item;
            }, $this->data);
            $this->data = array_chunk($this->data, 3000);
            foreach ($this->data as &$temp) {
                $sql = Formater::sqlInsertAll($table, $temp, $update);
                DB::insert($sql);
                unset($temp);
            }
        }
        return true;
    }

    //MRP库存同步bigdata_t_sku_stock mrp_base_sku_stock_lists

    public function syncBaseCore()
    {
        //mrp_sku_core_v3，mrp_sku_core_v3_sf	mrp_base_sku_core 备货关系表
        Log::info("初始化:mrp_base_sku_core");
        $table = 'mrp_base_sku_core';
        DB::table($table)->truncate();
        for ($i = 0; $i < 20; $i++) {
            $start = $i * $this->limit;
            $this->data = DB::connection('hz')->select("
                    SELECT sku,
                           1 type,
                           stock_way,
                           sales_status,
                           buffer_stock_cycle,
                           active_stock_cycle,
                           fixed_stock_num,
                           supply_cycle,
                           stock_advance_cycle,
                           stock_cycle,
                           remark,
                           sku_mark,
                           create_user created_user,
                           create_time created_at,
                           last_update_user updated_user,
                           last_update_date updated_at
                    FROM mrp_sku_core_v3 LIMIT $start,$this->limit
                ");
            if (empty($this->data)) {
                break;
            }
            $this->insertData($table);
        }
        //mrp_sku_core_v3_sf
        for ($i = 0; $i < 20; $i++) {
            $start = $i * $this->limit;
            $this->data = DB::connection('hz')->select("
                    SELECT
                           sku,
                           2 type,
                           stock_way,
                           sales_status,
                           buffer_stock buffer_stock_cycle,
                           stock_quantity fixed_stock_num,
                           supply_cycle,
                           prod_mark sku_mark,
                           create_user created_user,
                           create_time created_at,
                           last_update_user updated_user,
                           last_update_date updated_at
                    FROM mrp_sku_core_v3_sf LIMIT $start,$this->limit
                ");
            if (empty($this->data)) {
                break;
            }
            $this->insertData($table);
        }
        Log::info("初始化:mrp_base_sku_core 结束");
    }

    public function syncBaseStock()
    {
        $table = 'mrp_base_sku_stock_lists';
        DB::table($table)->truncate();
        for ($i = 0; $i < 20; $i++) {
            $start = $i * $this->limit;
            $this->data = DB::connection('hz')->select("
                    SELECT sku,
                           un_po_qty no_order_pr_num,
                           po_qty purchase_on_way_num,
                           act_stock actual_stock_num,
                           unprint_qty no_print_num,
                           occupy_stock occupy_stock_num,
                           newwms_use_qty newwms_use_num
                    FROM bigdata_t_sku_stock LIMIT $start,$this->limit
                ");
            if (empty($this->data)) {
                break;
            }
            $this->insertData($table);
        }
    }

    /**
     * 上线初同步基础数据
     * 1、进销存库存
     * @return array
     */
    public function iniData()
    {
        set_time_limit(3600);
        ini_set('memory_limit', '4096M');
        $qstart = Carbon::now()->format('Y-m-d H:i:s');
        //$this->iniStock();
        $this->iniPrice();
        return ['start' => $qstart, 'end' => Carbon::now()->format('Y-m-d H:i:s')];
    }

    /**
     * 价格初始化
     */
    public function iniPrice()
    {
        $table = 'mrp_base_sku_info_lists';
        $key = ['price','supplier_min_price','last_war_price'];
        for ($i = 0; $i < 50; $i++) {
            $start = $i * $this->limit;
            $data = DB::connection('hz')->select("
                    SELECT sku, skusystem_price price, supplier_min_price,last_war_price
                    FROM sku_newerp_price  LIMIT $start,$this->limit ");
            if (empty($data)) {
                break;
            }
            $this->data = $data;
            $this->insertData($table, $key);
        }
    }


    //插入数据方法

    /**
     * 同步进销存库存
     */
    public function iniStock()
    {
        $tableOmsPms = 'base_stock_oms_pms_lists';
        //DB::table($tableOmsPms)->truncate();
        $keyOmsPms = ['occupy_stock_num', 'purchase_on_way_num'];
        $tableWms = 'base_stock_order_use_qty_lists';
        //DB::table($tableOmsPms)->truncate();
        $keyWms = ['sku_num', 'newwms_use_num', 'leave_num'];
        for ($i = 0; $i < 20; $i++) {
            $start = $i * $this->limit;
            $data = DB::connection('hz')->select("
                    SELECT sku,
                           warehouseid,
                           order_use_qty occupy_stock_num,
                           purchase_on_way_qty purchase_on_way_num,
                           sku_qty sku_num,
                           newwms_use_qty newwms_use_num,
                           leave_qty leave_num
                    FROM stock_order_use_qty WHERE warehouseid IN(3,6)  LIMIT $start,$this->limit
                ");
            if (empty($data)) {
                break;
            }
            $dataWms = $dataOmsPms = [];
            foreach ($data as $v) {
                $tmp = ['sku' => $v->sku, 'warehouseid' => $v->warehouseid + 100];
                $dataOmsPms[] = array_merge(
                    $tmp,
                    ['occupy_stock_num' => $v->occupy_stock_num, 'purchase_on_way_num' => $v->purchase_on_way_num]
                );
                $dataWms[] = array_merge(
                    $tmp,
                    ['sku_num' => $v->sku_num, 'newwms_use_num' => $v->newwms_use_num, 'leave_num' => $v->leave_num]
                );
            }
            $this->data = $dataOmsPms;
            $this->insertData($tableOmsPms, $keyOmsPms);
            $this->data = $dataWms;
            $this->insertData($tableWms, $keyWms);
        }
    }


    //验证数据准确性

    public function validate()
    {
        $this->validateMrpResultPlanSf();
        $this->validateMrpResultPlanV3();
    }


    private function validateMrpResultPlanSf()
    {
        //检查条数
        $count1 = DB::table('mrp_result_plan_sf')->where('confirm_status', '!=', -2)
            ->count();
        $count2 = DB::connection('hz')->table('mrp_sku_plan_sf')->where('confirm_status', '!=', -2)
            ->count();


        if ($count1 != $count2) {
            Log::error("validateMrpResultPlanSf:数量验证不一致 count1:{$count1} ,count2:{$count2}");
        }
        //检查各个字段的总数量
        DB::table('mrp_result_plan_sf')->where('confirm_status', '!=', -2)
            ->chunkById(1000, function ($items) {
                //验证下基础字段
                $skuList = $items->pluck('sku');
                $hzItems = DB::connection('hz')->table('mrp_sku_plan_sf')
                    ->where('confirm_status', '!=', -2)
                    ->whereIn('sku', $skuList)->get()->keyBy('sku');
                foreach ($items as $item) {
                    $hzItem = $hzItems[$item->sku] ?? '';
                    if ($hzItem) {
                        if ($hzItem->stock_way != $item->stock_way) {
                            Log::error("{$item->sku},validateMrpResultPlanSf:stock_way验证不一致");
                        }
                        if ($hzItem->sales_status != $item->sales_status) {
                            Log::error("{$item->sku},validateMrpResultPlanSf:sales_status 验证不一致");
                        }
                        if ($hzItem->replenishment_num != $item->replenishment_num) {
                            Log::error("{$item->sku},validateMrpResultPlanSf:replenishment_num验证不一致");
                        }

                        if ($hzItem->order_point != $item->order_point) {
                            Log::error("{$item->sku},validateMrpResultPlanSf:order_point 验证不一致");
                        }

                        if ($hzItem->nearly1days_qty != $item->nearly1days_qty) {
                            Log::error("{$item->sku},validateMrpResultPlanSf:nearly1days_qty 验证不一致");
                        }
                        if ($hzItem->nearly1days_qty != $item->nearly1days_qty) {
                            Log::error("{$item->sku},validateMrpResultPlanSf:nearly1days_qty 验证不一致");
                        }
                        if ($hzItem->nearly2days_qty != $item->nearly2days_qty) {
                            Log::error("{$item->sku},validateMrpResultPlanSf:nearly2days_qty 验证不一致");
                        }
                        if ($hzItem->nearly3days_qty != $item->nearly3days_qty) {
                            Log::error("{$item->sku},validateMrpResultPlanSf:nearly3days_qty 验证不一致");
                        }
                        if ($hzItem->sales_trend != $item->sales_trend) {
                            Log::error("{$item->sku},validateMrpResultPlanSf:sales_trend 验证不一致");
                        }

                        if ($hzItem->pr_count != $item->pr_count) {
                            Log::error("{$item->sku},validateMrpResultPlanSf:pr_count 验证不一致");
                        }

                        if ($hzItem->day_sales != $item->day_sales) {
                            Log::error("{$item->sku},validateMrpResultPlanSf:day_sales 验证不一致");
                        }

                        if ($hzItem->purchase_on_way_num != $item->purchase_on_way_num) {
                            Log::error("{$item->sku},validateMrpResultPlanSf:purchase_on_way_num 验证不一致");
                        }


                        if ($hzItem->available_stock != $item->available_stock_num) {
                            Log::error("{$item->sku},validateMrpResultPlanSf:available_stock_num 验证不一致");
                        }

                        if ($hzItem->act_stock != $item->actual_stock_num) {
                            Log::error("{$item->sku},validateMrpResultPlanSf:actual_stock_num 验证不一致");
                        }

                        if ($hzItem->newwms_use_qty != $item->newwms_use_num) {
                            Log::error("{$item->sku},validateMrpResultPlanSf:newwms_use_num 验证不一致");
                        }

                        if ($hzItem->occupy_stock != $item->occupy_stock_num) {
                            Log::error("{$item->sku},validateMrpResultPlanSf:occupy_stock_num 验证不一致");
                        }

                        if ($hzItem->price != $item->price) {
                            Log::error("{$item->sku},validateMrpResultPlanSf:price 验证不一致");
                        }

                        if ($hzItem->total_stock != $item->total_stock_num) {
                            Log::error("{$item->sku},validateMrpResultPlanSf:total_stock_num 验证不一致");
                        }
                    } else {
                        Log::error("validateMrpResultPlanSf:{$item->sku}不存在");
                    }
                }
            });
        Log::info("validateMrpResultPlanSf:验证结束");
    }


    public function validateMrpReportOosOrdersDetailDaily()
    {
        $count1 = DB::table('mrp_report_oos_orders_detail_daily')
            ->where('dw_date', '=', Carbon::now()->format('Y-m-d'))->count();

        $count2 = DB::connection('hz')->table('mrp_sku_oos_orders_detail_daily')
            ->where('dw_date', '=', Carbon::now()->format('Y-m-d'))->count();

        if ($count1 != $count2) {
            Log::error("validateMrpReportOosOrdersDetailDaily:数量验证不一致 count1:{$count1} ,count2:{$count2}");
        }
        Log::info("validateMrpReportOosOrdersDetailDaily:验证结束");
    }


    public function validateMrpReportOosOrdersDetailTotal()
    {
        $count1 = DB::table('mrp_report_oos_orders_detail_total')
            ->where('dw_date', '=', Carbon::now()->format('Y-m-d'))->count();
        $count2 = DB::connection('hz')->table('mrp_sku_oos_orders_detail_total')
            ->where('dw_date', '=', Carbon::now()->format('Y-m-d'))->count();
        if ($count1 != $count2) {
            Log::error("validateMrpReportOosOrdersDetailTotal:数量验证不一致 count1:{$count1} ,count2:{$count2}");
        }

        Log::info("validateMrpReportOosOrdersDetailTotal:验证结束");
    }


    /**
     * Desc: 验证库存
     */
    public function validateBaseStockOrderUseQtyLists()
    {
        Log::info("validateBaseStockOrderUseQtyLists:验证开始");
        //检查条数
        $count1 = DB::table('base_stock_order_use_qty_lists')
            ->where('sku_num', '!=', 0)
            ->orWhere('newwms_use_num', '!=', 0)
            ->orWhere('leave_num', '!=', 0)
            ->count();
        $count2 = DB::connection('hz')
            ->table('stock_order_use_qty')
            ->where('sku_qty', '!=', 0)
            ->orWhere('newwms_use_qty', '!=', 0)
            ->orWhere('leave_qty', '!=', 0)
            ->count();
        if ($count1 != $count2) {
            Log::error("validateBaseStockOrderUseQtyLists:数量验证不一致 count1:{$count1} ,count2:{$count2}");
        }
        DB::table('base_stock_order_use_qty_lists')
            ->where('warehouseid', '=', 103)
            ->where(function ($query) {
                $query->where('sku_num', '!=', 0)
                    ->orWhere('newwms_use_num', '!=', 0)
                    ->orWhere('leave_num', '!=', 0);
            })
            ->select(['sku', 'sku_num', 'newwms_use_num', 'leave_num', 'id'])
            ->chunkById(2000, function ($items) {
                $skus = $items->pluck('sku');
                $hzItems = DB::connection('hz')
                    ->table('stock_order_use_qty')
                    ->select(['sku', 'sku_qty', 'newwms_use_qty', 'leave_qty'])
                    ->where('warehouseid', 3)
                    ->whereIn('sku', $skus)
                    ->get()->keyBy('sku');
                foreach ($items as $item) {
                    $hzItem = $hzItems[$item->sku] ?? '';
                    if ($hzItem) {
                        if ($hzItem->newwms_use_qty != $item->newwms_use_num) {
                            Log::error("{$item->sku},validateBaseStockOrderUseQtyLists:newwms_use_num 验证不一致");
                        }
                        if ($hzItem->sku_qty != $item->sku_num) {
                            Log::error("{$item->sku},validateBaseStockOrderUseQtyLists:sku_num 验证不一致");
                        }
                        if ($hzItem->leave_qty != $item->leave_num) {
                            Log::error("{$item->sku},validateBaseStockOrderUseQtyLists:leave_num 验证不一致");
                        }
                    } else {
                        Log::error("validateBaseStockOrderUseQtyLists:{$item->sku}不存在");
                    }
                }
            });

        DB::table('base_stock_order_use_qty_lists')
            ->where('warehouseid', '=', 106)
            ->where(function ($query) {
                $query->where('sku_num', '!=', 0)
                    ->orWhere('newwms_use_num', '!=', 0)
                    ->orWhere('leave_num', '!=', 0);
            })
            ->select(['sku', 'sku_num', 'newwms_use_num', 'leave_num', 'id'])
            ->chunkById(2000, function ($items) {
                $skus = $items->pluck('sku');
                $hzItems = DB::connection('hz')
                    ->table('stock_order_use_qty')
                    ->select(['sku', 'sku_qty', 'newwms_use_qty', 'leave_qty'])
                    ->where('warehouseid', 6)
                    ->whereIn('sku', $skus)
                    ->get()->keyBy('sku');
                foreach ($items as $item) {
                    $hzItem = $hzItems[$item->sku] ?? '';
                    if ($hzItem) {
                        if ($hzItem->newwms_use_qty != $item->newwms_use_num) {
                            Log::error("{$item->sku},validateBaseStockOrderUseQtyLists:newwms_use_num 验证不一致");
                        }
                        if ($hzItem->sku_qty != $item->sku_num) {
                            Log::error("{$item->sku},validateBaseStockOrderUseQtyLists:sku_num 验证不一致");
                        }
                        if ($hzItem->leave_qty != $item->leave_num) {
                            Log::error("{$item->sku},validateBaseStockOrderUseQtyLists:leave_num 验证不一致");
                        }
                    } else {
                        Log::error("validateBaseStockOrderUseQtyLists:{$item->sku}不存在");
                    }
                }
            });
        Log::info("validateBaseStockOrderUseQtyLists:验证结束");
    }


    public function validateBaseStockOmsPmsLists()
    {
        Log::info("validateBaseStockOmsPmsLists:验证开始");

        $count1 = DB::table('base_stock_oms_pms_lists')
            ->where('occupy_stock_num', '!=', 0)
            ->orWhere('purchase_on_way_num', '!=', 0)
            ->count();
        $count2 = DB::connection('hz')
            ->table('stock_order_use_qty')
            ->where('order_use_qty', '!=', 0)
            ->orWhere('purchase_on_way_qty', '!=', 0)
            ->count();
        if ($count1 != $count2) {
            Log::error("validateBaseStockOmsPmsLists:数量验证不一致 count1:{$count1} ,count2:{$count2}");
        }
        DB::table('base_stock_oms_pms_lists')
            ->where('warehouseid', '=', 103)
            ->where(function ($query) {
                $query->where('occupy_stock_num', '!=', 0)
                    ->orWhere('purchase_on_way_num', '!=', 0);
            })
            ->select(['sku', 'occupy_stock_num', 'purchase_on_way_num', 'id'])
            ->chunkById(2000, function ($items) {
                $skus = $items->pluck('sku');
                $hzItems = DB::connection('hz')
                    ->table('stock_order_use_qty')
                    ->select(['sku', 'order_use_qty', 'purchase_on_way_qty'])
                    ->where('warehouseid', 3)
                    ->whereIn('sku', $skus)
                    ->get()->keyBy('sku');
                foreach ($items as $item) {
                    $hzItem = $hzItems[$item->sku] ?? '';
                    if ($hzItem) {
                        if ($hzItem->order_use_qty != $item->occupy_stock_num) {
                            Log::error("{$item->sku},validateBaseStockOmsPmsLists:occupy_stock_num 验证不一致,数量:".abs($hzItem->order_use_qty - $item->occupy_stock_num));
                        }
                        if ($hzItem->purchase_on_way_qty != $item->purchase_on_way_num) {
                            Log::error("{$item->sku},validateBaseStockOmsPmsLists:purchase_on_way_num 验证不一致,数量:".abs($hzItem->purchase_on_way_qty - $item->purchase_on_way_num));
                        }
                    } else {
                        Log::error("validateBaseStockOmsPmsLists:{$item->sku}不存在");
                    }
                }
            });

        DB::table('base_stock_oms_pms_lists')
            ->where('warehouseid', '=', 106)
            ->where(function ($query) {
                $query->where('occupy_stock_num', '!=', 0)
                    ->orWhere('purchase_on_way_num', '!=', 0);
            })
            ->select(['sku', 'occupy_stock_num', 'purchase_on_way_num', 'id'])
            ->chunkById(2000, function ($items) {
                $skus = $items->pluck('sku');
                $hzItems = DB::connection('hz')
                    ->table('stock_order_use_qty')
                    ->select(['sku', 'order_use_qty', 'purchase_on_way_qty'])
                    ->where('warehouseid', 6)
                    ->whereIn('sku', $skus)
                    ->get()->keyBy('sku');
                foreach ($items as $item) {
                    $hzItem = $hzItems[$item->sku] ?? '';
                    if ($hzItem) {
                        if ($hzItem->order_use_qty != $item->occupy_stock_num) {
                            Log::error("{$item->sku},validateBaseStockOmsPmsLists:occupy_stock_num 验证不一致,数量:".abs($hzItem->order_use_qty - $item->occupy_stock_num));
                        }
                        if ($hzItem->purchase_on_way_qty != $item->purchase_on_way_num) {
                            Log::error("{$item->sku},validateBaseStockOmsPmsLists:purchase_on_way_num 验证不一致,数量:".abs($hzItem->purchase_on_way_qty - $item->purchase_on_way_num));
                        }
                    } else {
                        Log::error("validateBaseStockOmsPmsLists:{$item->sku}不存在");
                    }
                }
            });


        Log::info("validateBaseStockOmsPmsLists:验证结束");
    }

    /** V3版本报表验证 */
    public function validateV3(){
        set_time_limit(3600);
        ini_set('memory_limit', '4096M');
        $this->validateMrpResultPlanV3();
        $this->validateMrpReportOrigSalesdataModDetailV3();
        $this->validateMrpReportOrdersModBefDetailV3();
        $this->validateMrpReportOrdersModAftV3();
        $this->validateMrpReportStockCountV3();
        $this->validateMrpReportOrdersModBefV3();
    }

    ///MRP(国内)-》MRP V3-》计算SKU自动补货
    public function validateMrpResultPlanV3()
    {
        $this->validateHandelBySku(
            'mrp_result_plan_v3',
            [['confirm_status', '=', 1]],
            'mrp_sku_plan_v3',
            [['confirm_status', '=', 1]],
            [
                'order_day_times_14'=>'order_day_times_14',
                'day_sales_14'=>'day_sales_14',
                'sdv_day_sales'=>'sdv_day_sales',
                'nearly14days_qty'=>'nearly14days_qty',
                'sales_trend'=>'sales_trend',
                'stocking_coefficient'=>'stocking_coefficient',
                'pr_count'=>'pr_count',
                'purchase_on_way_num'=>'purchase_on_way_num',
                'available_stock_num'=>'available_stock',
                'actual_stock_num'=>'act_stock',
                'newwms_use_num'=>'newwms_use_qty',
                'occupy_stock_num'=>'occupy_stock',
                'total_stock_num'=>'total_stock',
                'leave_num'=>'leave_qty',
                'price'=>'price',
                'order_point'=>'order_point',
                'replenishment_num'=>'replenishment_num',
            ]
        );
    }

    //MRP(国内)-》MRP V3-》修正前销售明细统计表
    public function validateMrpReportOrigSalesdataModDetailV3(){
        $compute_batch = (new ReportService())->getComputeBatch();
        $this->validateHandelBySku(
            'mrp_report_orig_salesdata_mod_detail_v3',
            [['compute_batch', '=', $compute_batch]],
            'mrp_sku_orig_salesdata_mod_detail_v3',
            [['compute_batch', '=', $compute_batch]],
        );
    }

    //MRP(国内)-》MRP V3-》修正后销售明细统计表
    public function validateMrpReportOrdersModBefDetailV3(){
        $compute_batch = (new ReportService())->getComputeBatch();
        $this->validateHandelBySku(
            'mrp_report_orders_mod_bef_detail_v3',
            [['compute_batch', '=', $compute_batch]],
            'mrp_sku_orders_mod_bef_detail_v3',
            [['compute_batch', '=', $compute_batch]],
        );
    }

    //MRP(国内)-》MRP V3-》SKU销量统计（修正后）
    public function validateMrpReportOrdersModAftV3(){
        $compute_batch = (new ReportService())->getComputeBatch();
        $this->validateHandelBySku(
            'mrp_report_orders_mod_aft_v3',
            [['compute_batch', '=', $compute_batch]],
            'mrp_sku_orders_mod_aft_v3',
            [['compute_batch', '=', $compute_batch]],
        );
    }

    //MRP(国内)-》MRP V3-》SKU库存统计
    public function validateMrpReportStockCountV3(){
        $compute_batch = (new ReportService())->getComputeBatch();
        $this->validateHandelBySku(
            'mrp_report_stock_count_v3',
            [['compute_batch', '=', $compute_batch]],
            'mrp_sku_stock_count_v3',
            [['compute_batch', '=', $compute_batch]],
        );
    }

    //MRP(国内)->MRP V3->SKU日销量统计（修正前）
    public function validateMrpReportOrdersModBefV3(){
        $compute_batch = (new ReportService())->getComputeBatch();
        $this->validateHandelBySku(
            'mrp_report_orders_mod_bef_v3',
            [['compute_batch', '=', $compute_batch]],
            'mrp_sku_orders_mod_bef_v3',
            [['compute_batch', '=', $compute_batch]],
        );
    }

    /**
     * @param $pmsTable PMS表名
     * @param $pmsWhere PMS查询条件
     * @param $hzTable 进销存表名
     * @param $hzWhere 进销存查询条件
     * @param $validateKey 验证字段
     */
    public function validateHandelBySku($pmsTable,$pmsWhere,$hzTable,$hzWhere,$validateKey=[])
    {
        Log::info("$pmsTable:验证开始");

        $dataPmspro = DB::table($pmsTable)->where($pmsWhere)
            ->get()->toArray();
        $dataPmspro = array_column($dataPmspro,null,'sku');
        if(empty($dataPmspro)) {
            Log::info("PMS数据为空");
            return ;
        }
        //移除字段
        $removeKey = ['id','sku','warehouseid','sku_ware_record','platform','payment_date','compute_batch','create_at','created_at','updated_at'];
        $dataHz = DB::connection('hz')->table($hzTable)->where($hzWhere)
            ->get()->toArray();
        $dataHz = array_column($dataHz,null,'sku');
        if(empty($dataHz)) {
            Log::info("HZ数据为空");
            return ;
        }
        //获取默认更新字段
        if(empty($validateKey)) {
            $tmpData = current($dataPmspro);
            $validateKey = array_keys((array)$tmpData);
            $validateKey = array_diff($validateKey,$removeKey);
            $tmpData = current($dataHz);
            //取进销存与pms共有的字段
            $validateKey = array_intersect(array_keys((array)$tmpData),$validateKey);
            $validateKey = array_combine($validateKey,$validateKey);
        }
        //检查条数
        $count1 = count($dataPmspro);
        $count2 = count($dataHz);
        if ($count1 != $count2) {
            Log::error("数量验证不一致 pmspro:{$count1} ,hz:{$count2}");
        }
        $diffPmsHz = array_diff(array_keys($dataPmspro),array_keys($dataHz));
        $diffHzPms = array_diff(array_keys($dataHz),array_keys($dataPmspro));
        if(!empty($diffPmsHz)) Log::error("PMS有HZ无:".json_encode($diffPmsHz));
        if(!empty($diffHzPms)) Log::error("HZ有PMS无:".json_encode($diffHzPms));
        //检查各个字段的总数量
        foreach ($dataPmspro as $k=>$v){
            $error = '';
            if(in_array($k,$diffPmsHz)) continue;
            foreach ($validateKey as $vpms=>$vhz){
                $tmpPms = round($v->$vpms??0,2);
                $tmpHz = round($dataHz[$k]->$vhz??0,2);
                if($tmpPms != $tmpHz) $error .= $vpms.':pms '.$tmpPms.' hz '.$tmpHz.';';
            }
            if(!empty($error)) Log::info($k." 参数不一致 ".$error);
        }
        Log::info("$pmsTable:验证结束");
    }
}
