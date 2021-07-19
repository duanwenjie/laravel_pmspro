<?php
/**
 * MRP V3报表生成及补货计算
 * User: jiangshilin(jiangshilin@youkeshu.com)
 * Date: 2021/5/18
 * Time: 16:27
 */


namespace App\Services\MrpBaseData;

use App\Models\Mrp\MrpBaseSkuCore;
use App\Models\Mrp\MrpReportOrdersModAftV3;
use App\Models\Mrp\MrpReportOrdersModBefDetailV3;
use App\Models\Mrp\MrpReportOrdersModBefV3;
use App\Models\Mrp\MrpReportOrigSalesdataDetailV3;
use App\Models\Mrp\MrpReportOrigSalesdataDetailV3New;
use App\Models\Mrp\MrpReportOrigSalesdataModDetailV3;
use App\Models\Mrp\MrpReportOrigSalesdataModV3;
use App\Models\Mrp\MrpReportOrigSalesdataV3;
use App\Models\Mrp\MrpReportStockCountV3;
use App\Models\Mrp\MrpResultPlanV3;
use App\Models\MrpBaseData\MrpBaseOmsSalesList;
use App\Models\MrpBaseData\MrpTmpDwOprOrdersModBefV3;
use App\Models\MrpBaseData\MrpTmpMrpSkuPlanV3;
use App\Models\MrpBaseData\MrpTmpSalesDateItemCount;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;

class MrpReportV3Service extends ReportService
{
    //不计算销量的oms包裹状态
    const NOT_SALES_STATUS = [
        4 => '已取消',
        5 => '已撤单',
        6 => '待审核',
        7 => '未发货且已推送',
        9 => '平台撤单',
        15 => '已删除',
        18 => '已退件'
    ];

    /**
     * 跑当前所有报表
     */
    public function runData()
    {
        $start = time();
        $this->mrpReportOrigSalesdataModV3();//MRP(国内)-》MRP V3-》销量源数据（修正后）
        $this->mrpReportOrigSalesdataDetailV3();//MRP(国内)-》MRP V3-》销量明细统计表
        $this->mrpReportOrigSalesdataDetailV3New();//MRP(国内)-》MRP V3-》销量明细统计表(剔除海狮，BB业务线)
        $this->mrpReportOrigSalesdataModDetailV3();//MRP(国内)-》MRP V3-》修正前销售明细统计表
        $this->mrpReportOrdersModBefDetailV3();//MRP(国内)-》MRP V3-》修正后销售明细统计表
        $this->mrpReportOrdersModAftV3();//MRP(国内)-》MRP V3-》SKU销量统计（修正后）
        $this->mrpReportOrigSalesdataV3();//MRP(国内)-》MRP V3-》销量源数据
        $this->mrpReportStockCountV3();//MRP(国内)-》MRP V3-》SKU库存统计
        $this->mrpResultPlanV3();//MRP(国内)-》MRP V3-》计算SKU自动补货
        $this->mrpReportOrdersModBefV3();//MRP(国内)->MRP V3->SKU日销量统计（修正前）
        $end = time();
        $usetime = $end - $start;
        return $usetime . 'S';
    }

    /**
     * 进销存表名：mrp_sku_orig_salesdata_mod_v3
     * 进销存菜单：MRP(国内)-》MRP V3-》销量源数据（修正后）
     * 去除无用字段：发货时间，出库时间，创建时间（平台），创建时间（进ERP），仓库名称
     * 新表名:mrp_report_orig_salesdata_mod_v3
     * 逻辑：近14天销量（包含今天），当日销量大于5时修正成2 ，不剔除平台
     * @return int
     */
    public function mrpReportOrigSalesdataModV3()
    {
        MrpReportOrigSalesdataModV3::query()->truncate();
        $computeBatch = $this->getComputeBatch();
        $oldDay13 = Carbon::now()->subDays(13)->format('Y-m-d');
        $search = MrpBaseOmsSalesList::query()
            ->select(
                'package_code',
                'sku',
                DB::raw('(if(item_count>5,2,item_count)) as item_count'),
                'platform_code',
                'warehouseid',
                'payment_date',
                DB::raw("'$computeBatch' as compute_batch")
            )
            ->where('payment_date', '>=', $oldDay13)
            ->whereNotIn('order_status', array_keys(self::NOT_SALES_STATUS));
        return DB::table('mrp_report_orig_salesdata_mod_v3')->insertUsing([
            'package_code',
            'sku',
            'item_count',
            'platform_code',
            'warehouseid',
            'payment_date',
            'compute_batch'
        ], $search);
    }

    /**
     * 进销存表名：mrp_sku_orig_salesdata_detail_v3
     * 进销存菜单：MRP(国内)-》MRP V3-》销量明细统计表
     *  14天销量：以当前日期为第一天统计销售订单数据
     * sdv_day_sales：计算sku离散系数（14天日销量）：使用excel函数STDEV 验证
     * 参考文档：https://www.wps.cn/learning/course/detail/id/230
     * 按sku计算样本标准差算法:
     * item_count_p：sku每天销量平方
     * item_count：sku每天销量
     * 平方根：sqrt((sum(item_count_p) - sum(item_count) * sum(item_count) / 14 )/13)
     * 结果：平方根/14天日均销量((sum(item_count)/14))
     * 新表名:mrp_report_orig_salesdata_detail_v3
     * @return int
     */
    public function mrpReportOrigSalesdataDetailV3()
    {
        MrpReportOrigSalesdataDetailV3::query()->truncate();
        $computeBatch = $this->getComputeBatch();
        $oldDay13 = Carbon::now()->subDays(13)->format('Y-m-d');
        $tmpSearch = MrpBaseOmsSalesList::query()
            ->select(
                'sku',
                DB::raw('left(payment_date,10)  as payment_date'),
                DB::raw('sum(item_count) as item_count'),
                DB::raw('sum(item_count)*sum(item_count) as item_count_p')
            )
            ->where('payment_date', '>=', $oldDay13)
            ->whereNotIn('order_status', array_keys(self::NOT_SALES_STATUS))
            ->groupBy('sku')
            ->groupBy(DB::raw('left(payment_date,10)'));
        $selectFields = [
            'sku'
            ,
            DB::raw('round(sum(item_count)/14,3) as avg_day_sales')
            ,
            DB::raw('round(sqrt((sum(item_count_p) - sum(item_count) * sum(item_count) / 14 )/13)/(sum(item_count)/14),3) as sdv_day_sales')
            ,
            DB::raw("'$computeBatch' as compute_batch")
        ];
        $dateColumns = $this->getInsertDateColumnsByDays(range(1, 14), 'old_day_sales_');
        $selectFields = array_merge($selectFields, $dateColumns);
        $columns = ['sku', 'avg_day_sales', 'sdv_day_sales', 'compute_batch'];
        $columns = array_merge($columns, array_keys($dateColumns));
        $search = DB::table(DB::raw("({$tmpSearch->toSql()}) as t"))
            ->mergeBindings($tmpSearch->getQuery())
            ->select($selectFields)
            ->groupBy('sku');
        return DB::table('mrp_report_orig_salesdata_detail_v3')->insertUsing($columns, $search);
    }

    /**
     * 进销存表名：mrp_sku_orig_salesdata_detail_v3_new
     * 进销存菜单：MRP(国内)-》MRP V3-》销量明细统计表(剔除海狮，BB业务线)
     * 14天销量(剔除海狮，BB业务线)，计算：离散系数，日均销量
     *  14天销量：以当前日期为第一天统计销售订单数据
     * sdv_day_sales：计算sku离散系数（14天日销量）：使用excel函数STDEV 验证
     * 参考文档：https://www.wps.cn/learning/course/detail/id/230
     * 按sku计算样本标准差算法:
     * item_count_p：sku每天销量平方
     * item_count：sku每天销量
     * 平方根：sqrt((sum(item_count_p) - sum(item_count) * sum(item_count) / 14 )/13)
     * 结果：平方根/14天日均销量((sum(item_count)/14))
     * 新表名:mrp_report_orig_salesdata_detail_v3_new
     * @return int
     */
    public function mrpReportOrigSalesdataDetailV3New()
    {
        MrpReportOrigSalesdataDetailV3New::query()->truncate();
        $computeBatch = $this->getComputeBatch();
        $oldDay13 = Carbon::now()->subDays(13)->format('Y-m-d');
        $tmpSearch = MrpBaseOmsSalesList::query()
            ->select(
                'sku',
                DB::raw('left(payment_date,10)  as payment_date'),
                DB::raw('sum(item_count) as item_count'),
                DB::raw('sum(item_count)*sum(item_count) as item_count_p')
            )
            ->where('payment_date', '>=', $oldDay13)
            ->whereNotIn('platform_code', self::delPtCodeV3)
            ->whereNotIn('order_status', array_keys(self::NOT_SALES_STATUS))
            ->groupBy('sku')
            ->groupBy(DB::raw('left(payment_date,10)'));
        $selectFields = [
            'sku'
            ,
            DB::raw('round(sum(item_count)/14,3) as avg_day_sales')
            ,
            DB::raw('round(sqrt((sum(item_count_p) - sum(item_count) * sum(item_count) / 14 )/13)/(sum(item_count)/14),3) as sdv_day_sales')
            ,
            DB::raw("'$computeBatch' as compute_batch")
        ];
        $dateColumns = $this->getInsertDateColumnsByDays(range(1, 14), 'old_day_sales_');
        $selectFields = array_merge($selectFields, $dateColumns);
        $columns = ['sku', 'avg_day_sales', 'sdv_day_sales', 'compute_batch'];
        $columns = array_merge($columns, array_keys($dateColumns));
        $search = DB::table(DB::raw("({$tmpSearch->toSql()}) as t"))
            ->mergeBindings($tmpSearch->getQuery())
            ->select($selectFields)->groupBy('sku');
        return DB::table('mrp_report_orig_salesdata_detail_v3_new')->insertUsing($columns, $search);
    }

    /**
     * 进销存表名：mrp_sku_orig_salesdata_mod_detail_v3
     * 进销存菜单：MRP(国内)-》MRP V3-》修正前销售明细统计表
     * 新表名:mrp_report_orig_salesdata_mod_detail_v3
     * 根据 销量数据源mrp_report_orig_salesdata_mod_v3 做sku汇总
     */
    public function mrpReportOrigSalesdataModDetailV3()
    {
        //MrpReportOrigSalesdataModDetailV3::query()->truncate();
        $oldDay7 = Carbon::now()->subDays(7)->format('Y-m-d');
        $computeBatch = $this->getComputeBatch();
        MrpReportOrigSalesdataModDetailV3::query()
            ->where('compute_batch', '<', $oldDay7)
            ->orWhere('compute_batch', '=', $computeBatch)
            ->delete();
        $selectFields = ['sku', DB::raw("'$computeBatch' as compute_batch")];
        $dateColumns = $this->getInsertDateColumnsByDays(range(1, 14), 'old_day_sales_');
        $selectFields = array_merge($selectFields, $dateColumns);
        $columns = ['sku', 'compute_batch'];
        $columns = array_merge($columns, array_keys($dateColumns));
        $search = MrpReportOrigSalesdataModV3::query()
            ->select($selectFields)
            ->groupBy('sku');
        return DB::table('mrp_report_orig_salesdata_mod_detail_v3')->insertUsing($columns, $search);
    }

    /*
    进销存表名：mrp_sku_orders_mod_bef_detail_v3
    进销存菜单：MRP(国内)-》MRP V3-》修正后销售明细统计表
    新表名:mrp_report_orders_mod_bef_detail_v3
    */
    public function mrpReportOrdersModBefDetailV3()
    {
        $oldDay7 = Carbon::now()->subDays(7)->format('Y-m-d');
        $computeBatch = $this->getComputeBatch();
        MrpReportOrdersModBefDetailV3::query()
            ->where('compute_batch', '<', $oldDay7)
            ->orWhere('compute_batch', '=', $computeBatch)
            ->delete();
        $this->mrpTmpSalesDateItemCount();
        $this->mrpTmpDwOprOrdersModBefV3Sql();//启用sql插入方式,大概13秒
        //$this->mrpTmpDwOprOrdersModBefV3();//大约需要130秒
        $selectFields = ['sku', DB::raw("'$computeBatch' as compute_batch")];
        $dateColumns = $this->getInsertDateColumnsByDays(range(1, 14), 'old_day_sales_', 'condition_value');
        $selectFields = array_merge($selectFields, $dateColumns);
        $columns = ['sku', 'compute_batch'];
        $columns = array_merge($columns, array_keys($dateColumns));
        $search = MrpTmpDwOprOrdersModBefV3::query()->select($selectFields)->groupBy('sku');
        return DB::table('mrp_report_orders_mod_bef_detail_v3')->insertUsing($columns, $search);
    }

    /**
     * 中间表
     * 生成按日销量表数据（非报表）mrp_tmp_sales_date_item_counts;
     * 未修正销量（5修正成2，不剔除平台），只取V3平台近14天（包含今天）销量
     */
    private function mrpTmpSalesDateItemCount()
    {
        MrpTmpSalesDateItemCount::query()->truncate();
        $search = MrpReportOrigSalesdataModV3::query()->select(
            'sku',
            DB::raw('left(payment_date,10) payment_date'),
            DB::raw('sum(item_count) item_count')
        )
            ->whereNotIn('platform_code', self::delPtCodeV3)
            ->groupBy('sku')
            ->groupBy(DB::raw('left(payment_date,10)'));
        return DB::table('mrp_tmp_sales_date_item_counts')->insertUsing(['sku', 'payment_date', 'item_count'], $search);
    }

    /**
     * 中间表
     * 生成sku销量统计表数据（非报表）mrp_tmp_dw_opr_orders_mod_bef_v3 原进销存：bigdata_DW_OPR_ORDERS_MOD_BEF_V3
     */
    private function mrpTmpDwOprOrdersModBefV3()
    {
        MrpTmpDwOprOrdersModBefV3::query()->truncate();
        $start = time();
        MrpTmpSalesDateItemCount::query()
            ->select(['id', 'sku', 'payment_date', 'item_count'])
            ->chunkById(1000, function ($items) use (&$num, $start) {
                $skuList = $items->unique('sku')->pluck('sku');
                $coreInfo = $this->getSkuCoreLists($skuList, 1, ['sku', 'stock_way', 'sales_status']);
                $salesRange = $this->getMrpTmpSalesDateItemCountRange($skuList, [2, 6, 13]);
                $sdvDaySales = $this->getSkuSdvDaySales($skuList);
                $num += 1000;
                $tempInsert = [];
                foreach ($items as $v) {
                    $sku = $v['sku'];
                    if (empty($salesRange[$sku]['nearly2days_qty'])
                        && empty($salesRange[$sku]['nearly6days_qty'])
                        && empty($salesRange[$sku]['nearly13days_qty'])) {
                        continue;
                    }
                    $nearly3daysQty = round($salesRange[$sku]['nearly2days_qty'] ?? 0 / 3, 2);
                    $nearly7daysQty = round($salesRange[$sku]['nearly6days_qty'] ?? 0 / 7, 2);
                    $nearly14daysQty = round($salesRange[$sku]['nearly13days_qty'] ?? 0 / 14, 2);
                    /*是否触发销量调整:1触发,-1不触发
                    * 7天销量大于0 且 日销量/14天销量大于等于2 则触发，否则不触发
                    */
                    $mod_condition = $nearly7daysQty > 0 && ($v['item_count'] / $nearly14daysQty) >= 2 ? 1 : -1;
                    //触发值
                    $condition_value = $this->getConditionValue(
                        $v,
                        $nearly3daysQty,
                        $nearly7daysQty,
                        $nearly14daysQty,
                        $sdvDaySales[$sku]
                    );
                    $tempInsert[] = [
                        'sku' => $sku,
                        'stock_way' => $coreInfo[$sku]['stock_way'] ?? '',
                        'sales_status' => $coreInfo[$sku]['sales_status'] ?? '',
                        'nearly3days_qty' => $nearly3daysQty,
                        'nearly7days_qty' => $nearly14daysQty,
                        'payment_date' => $v['payment_date'],
                        'item_count' => $v['item_count'],
                        'mod_condition' => $mod_condition,
                        'condition_value' => $condition_value ?? 0,
                    ];
                }
                mrpTmpDwOprOrdersModBefV3::query()->insert($tempInsert);
                //$tmp_end = time() - $start;
                //Log::info("执行完{$num}条;".$tmp_end."s");
            });
    }

    /**
     * 中间表sql插入，不chunk
     * 生成sku销量统计表数据（非报表）mrp_tmp_dw_opr_orders_mod_bef_v3 原进销存：bigdata_DW_OPR_ORDERS_MOD_BEF_V3
     */
    private function mrpTmpDwOprOrdersModBefV3Sql()
    {
        MrpTmpDwOprOrdersModBefV3::query()->truncate();
        $selectFields = [
            'sku'
            ,
            DB::raw('round(sum(if(payment_date>=date_sub(CURDATE(),interval 2 day ),item_count,0))/3,2) nearly3days_qty')
            ,
            DB::raw('round(sum(if(payment_date>=date_sub(CURDATE(),interval 6 day ),item_count,0))/7,2) nearly7days_qty')
            ,
            DB::raw('round(sum(IF(payment_date>=date_sub(CURDATE(),interval 13 day ),item_count,0))/14,2) nearly14days_qty')
        ];
        $ak = MrpTmpSalesDateItemCount::query()->select($selectFields)
            ->groupBy('sku');
        $search = MrpTmpSalesDateItemCount::query()
            ->select([
                'ak.sku'
                ,
                'c.stock_way'
                ,
                'c.sales_status'
                ,
                'ak.nearly3days_qty'
                ,
                'ak.nearly7days_qty'
                ,
                'tc.payment_date'
                ,
                'tc.item_count'
                ,
                DB::raw('if(ak.nearly7days_qty >= 0 and tc.item_count/ak.nearly14days_qty >= 2,1 ,-1 ) mod_condition')
                ,
                DB::raw('(case
	when s.sdv_day_sales<=1.5 then
        (case when ak.nearly7days_qty > 0 and ak.nearly3days_qty/ak.nearly7days_qty >= 1.2 and tc.item_count/ak.nearly14days_qty >= 2
					 then ceil(2*ak.nearly14days_qty)
							when ((ak.nearly7days_qty > 0 and ak.nearly3days_qty/ak.nearly7days_qty < 1.2) or ak.nearly7days_qty = 0) and tc.item_count/ak.nearly14days_qty >= 2
					 then ceil(1.5*ak.nearly14days_qty)
					 else tc.item_count end)
	when s.sdv_day_sales>1.5 and s.sdv_day_sales<=2.5 then
        (case when ak.nearly7days_qty > 0 and ak.nearly3days_qty/ak.nearly7days_qty >= 1.2 and tc.item_count/ak.nearly14days_qty >= 2
						then ceil(1*ak.nearly14days_qty)
						when ((ak.nearly7days_qty > 0 and ak.nearly3days_qty/ak.nearly7days_qty < 1.2) or ak.nearly7days_qty = 0) and tc.item_count/ak.nearly14days_qty >= 2
						then ceil(0.5*ak.nearly14days_qty)
						else tc.item_count end)
	when s.sdv_day_sales>2.5 then
	    (case when tc.item_count/ak.nearly14days_qty >= 2 then 1 else tc.item_count end)
			end)
			 condition_value')
            ])
            ->from('mrp_tmp_sales_date_item_counts', 'tc')
            ->joinSub($ak, 'ak', function ($join) {
                $join->on('tc.sku', '=', 'ak.sku');
            })
            ->join('mrp_report_orig_salesdata_detail_v3 as s', function ($join) {
                $join->on('tc.sku', '=', 's.sku');
            })
            ->leftJoin('mrp_base_sku_core as c', function ($join) {
                $join->on('tc.sku', '=', 'c.sku')->on('c.type', '=', DB::raw(1));
            });
        $columns = [
            'sku',
            'stock_way',
            'sales_status',
            'nearly3days_qty',
            'nearly7days_qty',
            'payment_date',
            'item_count',
            'mod_condition',
            'condition_value'
        ];
        return DB::table('mrp_tmp_dw_opr_orders_mod_bef_v3')->insertUsing($columns, $search);
    }

    /**
     * 文档以：七月MRP新备货逻辑201217.pptx 为准
     * 算法：
     * C=前3天日均，D=前7天日均，A=前14天总销量
     * 离散系数≤1.5
     *D>0且C/D≥1.2（①X/（A/14）≥2，将X替换为2*（A/14）并向上取整,②X/（A/14）<2，X保持不变）
     *D>0且C/D<1.2（①X/（A/14）≥2，将X替换为1.5*（A/14）并向上取整,X/（A/14）<2，X保持不变）
     *D=0 (①X/（A/14）≥2，将X替换为1.5*（A/14）并向上取整,X/（A/14）<2，X保持不变)
     *  1.5＜离散系数≤2
     *D>0且C/D≥1.2（①X/（A/14）≥2，将X替换为1*（A/14）并向上取整,②X/（A/14）<2，X保持不变）
     *D>0且C/D<1.2（①X/（A/14）≥2，将X替换为0.5*（A/14）并向上取整,X/（A/14）<2，X保持不变）
     *D=0 (①X/（A/14）≥2，将X替换为0.5*（A/14）并向上取整,X/（A/14）<2，X保持不变)
     * *  2＜离散系数
     *D>0且C/D≥1.2（①X/（A/14）≥2，将X替换为1 ,②X/（A/14）<2，X保持不变）
     *D>0且C/D<1.2（①X/（A/14）≥2，将X替换为1 ,X/（A/14）<2，X保持不变）
     *D=0 (①X/（A/14）≥2，将X替换为1 并向上取整,X/（A/14）<2，X保持不变)
     * @param $item 日销量
     * @param $nearly3daysQty 3天日均
     * @param $nearly7daysQty 7天日均
     * @param $nearly14daysQty 14天日均
     * @param $sdvDaySales 离散系数
     * @return false|float|int
     */
    private function getConditionValue($item, $nearly3daysQty, $nearly7daysQty, $nearly14daysQty, $sdvDaySales)
    {
        if ($sdvDaySales <= 1.5) {
            if ($nearly7daysQty > 0
                && ($nearly3daysQty / $nearly7daysQty) >= 1.2
                && ($item['item_count'] / $nearly14daysQty) >= 2
            ) {
                return ceil(2 * $nearly14daysQty);
            }
            if ((($nearly7daysQty > 0 && ($nearly3daysQty / $nearly7daysQty) < 1.2)
                    || $nearly7daysQty == 0)
                && ($item['item_count'] / $nearly14daysQty) >= 2
            ) {
                return ceil(1.5 * $nearly14daysQty);
            }
            return $item['item_count'];
        } else {
            if ($sdvDaySales > 1.5 && $sdvDaySales <= 2.5) {
                if ($nearly7daysQty > 0
                    && ($nearly3daysQty / $nearly7daysQty) >= 1.2
                    && ($item['item_count'] / $nearly14daysQty) >= 2
                ) {
                    return ceil(1 * $nearly14daysQty);
                }
                if ((($nearly7daysQty > 0 && ($nearly3daysQty / $nearly7daysQty) < 1.2)
                        || $nearly7daysQty == 0)
                    && ($item['item_count'] / $nearly14daysQty) >= 2
                ) {
                    return ceil(0.5 * $nearly14daysQty);
                }
                return $item['item_count'];
            } else {
                if (($item['item_count'] / $nearly14daysQty) >= 2) {
                    return 1;
                } else {
                    return $item['item_count'];
                }
            }
        }
    }

    //获取sku v3离散系数
    public function getSkuSdvDaySales($skus)
    {
        return MrpReportOrigSalesdataDetailV3::query()->select(['sku', 'sdv_day_sales'])
            ->whereIn('sku', $skus)->pluck('sdv_day_sales', 'sku');
    }

    //获取SKU备货关系表数据
    private function getSkuCoreLists($skus, $type = 1, $columns)
    {
        $res = MrpBaseSkuCore::query()->select($columns)
            ->whereIn('sku', $skus)
            ->where('type', $type)->get()->toArray();
        return array_column($res, null, 'sku');
    }

    //临时表获取区间销量
    private function getMrpTmpSalesDateItemCountRange($skus, $range)
    {
        $selectFields = ['sku'];
        $dateColumns = $this->getDateColumnsByDays($range, true);
        $selectFields = array_merge($selectFields, $dateColumns);
        $res = MrpTmpSalesDateItemCount::query()->select($selectFields)
            ->whereIn('sku', $skus)
            ->groupBy('sku')->get()->toArray();
        return array_column($res, null, 'sku');
    }

    /**
     * 进销存表名：mrp_sku_orders_mod_aft_v3 ，bigdata_DW_OPR_ORDERS_MOD_AFT_V3
     * 进销存菜单：MRP(国内)-》MRP V3-》SKU销量统计（修正后）
     * 新表名:mrp_report_orders_mod_aft_v3
     * sales_trend:销量趋势:0平稳1上涨-1下降
     * 算法：
     * E=前3天日均，F=前6天日均，G=前13天日均
     * 10月11日之前的EFG，都以当前日期为第一天计算日均，10月11日改为以昨天日期为第一天，即E=前2天日均，F=前6天日均，G=前13天日均
     * 1、F>0且G>0 (1)上涨：E/F≥1且F/G≥1.2；(2)下降：E/F<1且F/G<1.2；(3)趋势不明：E/F<1且F/G≥1.2；(4)趋势不明：E/F≥1且F/G<1.2
     * 2、F=0或G=0 趋势不明：无需判定EFG比值
     * stocking_coefficient:备货系数
     * 算法：
     * 日均销量x,B=近两天(昨天和今天)日均,E=前3天日均,F=前6天日均,G=前13天日均
     * (1)当B=0，x=0，即当昨天和今天销量都为0时，用作备货的日均销量直接取0，最终只补缺货
     * (2)B>0，离散系数≤1，x=14天销量明细统计表中的日均销量
     * (3)B>0,离散系数＞1;上涨：x=0.5*E+0.3*F+0.2*G;下降：x=MIN(0.7*E+0.2*F+0.1*G,G);趋势不明：x=MIN(0.5*E+0.3*F+0.2*G,G)
     */
    public function mrpReportOrdersModAftV3()
    {
        MrpReportOrdersModAftV3::query()->truncate();
        $computeBatch = $this->getComputeBatch();
        $tmpSearch = MrpTmpDwOprOrdersModBefV3::query()->from('mrp_tmp_dw_opr_orders_mod_bef_v3', 'a')
            ->select([
                'sku',
                'stock_way',
                'sales_status'
                ,
                DB::raw('round(sum(case when a.payment_date >= date_sub(curdate(),interval 1 day ) then a.condition_value else 0 end)/2,2) nearly2days_qty')
                ,
                DB::raw('count(distinct a.payment_date) order_day_times_14')
                ,
                DB::raw('sum(a.condition_value) day_sales_14')
                ,
                DB::raw('round(sum(case when a.payment_date >= date_sub(curdate(),interval 3 day) and a.payment_date <>curdate() then a.condition_value else 0 end)/3,2) nearly3days_qty')
                ,
                DB::raw('round(sum(case when a.payment_date >= date_sub(curdate(),interval 6 day) and a.payment_date <>curdate() then a.condition_value else 0 end)/6,2) nearly7days_qty')
                ,
                DB::raw('round(sum(case when a.payment_date >= date_sub(curdate(),interval 13 day) and a.payment_date <>curdate() then a.condition_value else 0 end)/13,2) nearly13days_qty')
                ,
                DB::raw('round(sum(case when a.payment_date >= date_sub(curdate(),interval 13 day) then a.condition_value else 0 end)/14,2) nearly14days_qty')
            ])
            ->groupBy('sku');
        $columns = [
            'sku',
            'stock_way',
            'sales_status',
            'order_day_times_14',
            'nearly2days_qty',
            'day_sales_14',
            'nearly3days_qty',
            'nearly7days_qty',
            'nearly13days_qty',
            'nearly14days_qty',
            'sales_trend',
            'stocking_coefficient',
            'compute_batch',
            'updated_at',
            'create_at'
        ];
        $selectFields = [
            'sku',
            'stock_way',
            'sales_status',
            'order_day_times_14',
            'nearly2days_qty',
            'day_sales_14',
            'nearly3days_qty',
            'nearly7days_qty',
            'nearly13days_qty',
            'nearly14days_qty',
            DB::raw('(case when nearly7days_qty >0 and nearly13days_qty >0 then
       case when round(nearly3days_qty / nearly7days_qty,2) >= 1 and round(nearly7days_qty / nearly13days_qty,2) >= 1 then 1
       when round(nearly3days_qty / nearly7days_qty,2) < 1 and round(nearly7days_qty / nearly13days_qty,2) < 1 then -1
       else 0 end else 0 end)  sales_trend'),
            DB::raw('(case when nearly7days_qty >0 and nearly13days_qty >0 then
    case when round(nearly3days_qty / nearly7days_qty,2) >= 1 and round(nearly7days_qty / nearly13days_qty,2) >= 1 then 1.1
    when round(nearly3days_qty / nearly7days_qty,2) < 1 and round(nearly7days_qty / nearly13days_qty,2) < 1 then 0.5
    else 0.9  end  else 0.9 end) stocking_coefficient'),
            DB::raw("'$computeBatch' compute_batch"),
            DB::raw("now() create_at"),
            DB::raw("now() update_at")
        ];
        $search = DB::table(DB::raw("({$tmpSearch->toSql()}) as t"))
            ->mergeBindings($tmpSearch->getQuery())
            ->select($selectFields)->groupBy('sku');
        DB::table('mrp_report_orders_mod_aft_v3')->insertUsing($columns, $search);
        //李海林调整14天销量逻辑 离散系数＞1 E=前3天日均F=前6天日均G=前13天日均 销售趋势上涨：x=0.5*E+0.3*F+0.2*G 下降：x=MIN(0.7*E+0.2*F+0.1*G,G) 平稳：x=G
        //2020-11-28 李海林调整为 趋势不明（之前的平稳）：x=MIN(0.5*E+0.3*F+0.2*G,G)
        $update = [
            'af.nearly14days_qty' => DB::raw('(case
            when af.nearly2days_qty=0 then 0
            when af.nearly2days_qty>0 and s.sdv_day_sales<=1 then s.avg_day_sales
            else
                 (case when s.sdv_day_sales>1 and af.sales_trend=-1 then least(0.7*af.nearly3days_qty+0.2*af.nearly7days_qty+0.1*af.nearly13days_qty,af.nearly13days_qty)
                when s.sdv_day_sales>1 and af.sales_trend=0  then least(0.5*af.nearly3days_qty+0.3*af.nearly7days_qty+0.2*af.nearly13days_qty,af.nearly13days_qty)
                when s.sdv_day_sales>1 and af.sales_trend=1 then 0.5*af.nearly3days_qty+0.3*af.nearly7days_qty+0.2*af.nearly13days_qty
                else s.avg_day_sales end)
            end)')
        ];
        return MrpReportOrdersModAftV3::query()->from('mrp_report_orders_mod_aft_v3', 'af')
            ->join('mrp_report_orig_salesdata_detail_v3 as s', function ($join) {
                $join->on('af.sku', '=', 's.sku');
            })->update($update);
    }

    /**
     * 进销存表名：mrp_sku_orig_salesdata_v3
     * 进销存菜单：MRP(国内)-》MRP V3-》销量源数据
     * 新表名:mrp_report_orig_salesdata_v3
     */
    public function mrpReportOrigSalesdataV3()
    {
        $oldDay13 = Carbon::now()->subDays(13)->format('Y-m-d');
        MrpReportOrigSalesdataV3::query()->truncate();
        $computeBatch = $this->getComputeBatch();
        $search = MrpBaseOmsSalesList::query()->from('mrp_base_oms_sales_lists', 'a')
            ->leftJoin('mrp_base_sku_info_lists as p', function ($join) {
                $join->on('a.sku', '=', 'p.sku');
            })
            ->leftJoin('mrp_report_orig_salesdata_detail_v3 as d', function ($join) {
                $join->on('a.sku', '=', 'd.sku');
            })
            ->leftJoin('mrp_report_orders_mod_aft_v3 as af', function ($join) {
                $join->on('a.sku', '=', 'af.sku');
            })
            ->leftJoin('mrp_base_platform_lists as pm', function ($join) {
                $join->on('pm.platform_code','=','a.platform_code');
            })
            ->select([
                'a.package_code',
                'a.sku',
                'a.item_count',
                DB::raw('pm.platform_cn_name as platform'),
                'p.price',
                'd.avg_day_sales',
                'af.nearly14days_qty',
                'a.sales_account',
                'a.warehouseid',
                'a.payment_date',
                DB::raw("'$computeBatch' compute_batch"),
            ])
            ->where('a.payment_date', '>=', $oldDay13)
            ->whereNotIn('a.order_status', array_keys(self::NOT_SALES_STATUS));
        $columns = [
            'package_code',
            'sku',
            'item_count',
            'platform',
            'price',
            'avg_day_sales',
            'nearly14days_qty',
            'sales_account',
            'warehouseid',
            'payment_date',
            'compute_batch'
        ];
        MrpReportOrigSalesdataV3::query()->insertUsing($columns, $search);
        //business_type 一级部门；该字段更新比较慢，后期可以在报表里面连表 mrp_base_accounts_lists查询 一级部门
        MrpReportOrigSalesdataV3::query()->from('mrp_report_orig_salesdata_v3', 's')
            ->join('mrp_base_accounts_lists as a', function ($join) {
                $join->on('s.sales_account', '=', 'a.account');
            })->update([
                's.business_type' => DB::raw('a.department'),
                's.jl_account' => DB::raw('a.jl_account'),
                's.zg_account' => DB::raw('a.zj_account')//@todo 新系统字段建错，暂时用主管字段存储总监账号
            ]);
    }

    /**
     * 进销存表名：mrp_sku_stock_count_v3
     * 进销存菜单：MRP(国内)-》MRP V3-》SKU库存统计
     * 新表名:mrp_report_stock_count_v3
     */
    public function mrpReportStockCountV3()
    {
        MrpReportStockCountV3::query()->truncate();
        $computeBatch = $this->getComputeBatch();
        $search = MrpBaseSkuCore::query()->from('mrp_base_sku_core', 'a')
            ->leftJoin('mrp_report_orders_mod_aft_v3 as b', function ($join) {
                $join->on('a.sku', '=', 'b.sku');
            })
            ->leftJoin('mrp_base_sku_stock_lists as c', function ($join) {
                $join->on('a.sku', '=', 'c.sku');
            })
            ->leftJoin('mrp_base_sku_info_lists as aa', function ($join) {
                $join->on('a.sku', '=', 'aa.sku');
            })
            ->select([
                'a.sku',
                'a.stock_way',
                'a.sales_status',
                DB::raw('ifnull(b.order_day_times_14,0) order_day_times_14'),
                DB::raw('ifnull(a.fixed_stock_num,0) fixed_stock_num'),
                DB::raw('ifnull(b.day_sales_14,0) day_sales_14'),
                DB::raw('sum(ifnull(c.no_order_pr_num,0)) + sum(ifnull(c.no_print_num,0)) pr_count'),
                DB::raw('sum(ifnull(c.no_order_pr_num,0)) pr_no_po'),
                DB::raw('sum(ifnull(c.no_print_num,0)) pr_po_no_print'),
                DB::raw('sum(ifnull(c.purchase_on_way_num,0)) purchase_on_way_num'),
                DB::raw('sum(ifnull(c.actual_stock_num,0)) - sum(ifnull(c.occupy_stock_num,0)) - sum(ifnull(c.newwms_use_num,0)) available_stock'),
                DB::raw('sum(ifnull(c.actual_stock_num,0)) act_stock'),
                DB::raw('sum(ifnull(c.newwms_use_num,0)) newwms_use_qty'),
                DB::raw('sum(ifnull(c.leave_num,0)) leave_qty'),
                DB::raw('sum(ifnull(c.occupy_stock_num,0)) occupy_stock'),
                DB::raw('sum(ifnull(c.no_order_pr_num,0)) + sum(ifnull(c.no_print_num,0)) + sum(ifnull(c.purchase_on_way_num,0)) + sum(ifnull(c.actual_stock_num,0)) - sum(ifnull(c.occupy_stock_num,0)) - sum(ifnull(c.newwms_use_num,0)) total_stock'),
                DB::raw('IF(IF(aa.last_war_price>0,aa.last_war_price,aa.supplier_min_price)>0,IF(aa.last_war_price>0,aa.last_war_price,aa.supplier_min_price),aa.price) price'),
                DB::raw('case when a.stock_way = 1 or ifnull(b.order_day_times_14,0) <= 3 then 0 + ifnull(a.fixed_stock_num,0) else ceil(ifnull(a.stock_advance_cycle * b.nearly14days_qty,0)) + ifnull(a.fixed_stock_num,0) end  order_point'),
                DB::raw("'多次入库' sku_ware_record"),
                DB::raw('aa.main_warehouseid warehouseid'),
                'a.stock_cycle',
                'a.stock_advance_cycle',
                DB::raw('ifnull(b.nearly14days_qty,0) nearly14days_qty'),
                DB::raw('ifnull(b.stocking_coefficient,0) stocking_coefficient'),
                'b.sales_trend',
                DB::raw("'$computeBatch' compute_batch")
            ])
            ->whereIn('a.sales_status', [1, 2])
            ->where('a.type', '=', 1)
            ->groupBy('a.sku');
        $columns = [
            'sku',
            'stock_way',
            'sales_status',
            'order_day_times_14',
            'fixed_stock_num',
            'day_sales_14',
            'pr_count',
            'pr_no_po',
            'pr_po_no_print',
            'purchase_on_way_num',
            'available_stock',
            'act_stock',
            'newwms_use_qty',
            'leave_qty',
            'occupy_stock',
            'total_stock',
            'price',
            'order_point',
            'sku_ware_record',
            'warehouseid',
            'stock_cycle',
            'stock_advance_cycle',
            'nearly14days_qty',
            'stocking_coefficient',
            'sales_trend',
            'compute_batch'
        ];
        return DB::table('mrp_report_stock_count_v3')->insertUsing($columns, $search);
    }

    /**
     * 进销存表名：mrp_sku_plan_v3
     * 进销存菜单：MRP(国内)-》MRP V3-》SKU库存统计
     * 新表名:mrp_result_plan_v3
     */
    public function mrpResultPlanV3()
    {
        //生成报表前先撤销之前待处理的
        MrpResultPlanV3::query()->where('confirm_status', '=', '1')->update(['confirm_status' => '-2']);
        $computeBatch = $this->getComputeBatch();
        $this->mrpTmpMrpSkuPlanV3();
        $search = MrpTmpMrpSkuPlanV3::query()->from('mrp_tmp_mrp_sku_plan_v3', 'k')
            ->join('mrp_base_sku_core as core', function ($join) {
                $join->on('k.sku', '=', 'core.sku');
                $join->on('core.type', '=', DB::raw(1));
            })
            ->leftJoin('mrp_report_orig_salesdata_detail_v3 as bb', function ($join) {
                $join->on('k.sku', '=', 'bb.sku');
            })
            ->leftJoin('mrp_base_sku_stock_lists as o', function ($join) {
                $join->on('k.sku', '=', 'o.sku')->on('o.warehouseid', '=', DB::raw(103));
            })
            ->leftJoin('mrp_base_sku_info_lists as i', function ($join) {
                $join->on('k.sku', '=', 'i.sku');
            })
            ->select([
                'k.sku',
                'k.stock_way',
                'k.sales_status',
                'k.warehouseid',
                'k.stock_cycle',
                'k.stock_advance_cycle',
                'k.fixed_stock_num',
                'k.order_day_times_14',
                'k.day_sales_14',
                'bb.sdv_day_sales',
                'k.nearly14days_qty',
                'k.sales_trend',
                'k.stocking_coefficient',
                'k.pr_count',
                'k.purchase_on_way_num'
                ,
                DB::raw('available_stock available_stock_num')
                ,
                DB::raw('act_stock actual_stock_num')
                ,
                DB::raw('newwms_use_qty newwms_use_num')
                ,
                DB::raw('occupy_stock actual_stock_num')
                ,
                DB::raw('total_stock total_stock_num')
                ,
                'o.leave_num',
                'k.price',
                'order_point',
                DB::raw('case when replenishment_num >0 and k.price * replenishment_num < 10 and IFNULL(k.price,0)<>0
                        then
                            ceil(10/k.price)
                        else
                            replenishment_num
                        end replenishment_num')
                ,
                'core.remark',
                'request_date'
                ,
                DB::raw("'$computeBatch' compute_batch")
                ,
                'i.planner_nick'
            ]);
        $columns = [
            'sku',
            'stock_way',
            'sales_status',
            'warehouseid',
            'stock_cycle',
            'stock_advance_cycle',
            'fixed_stock_num',
            'order_day_times_14',
            'day_sales_14',
            'sdv_day_sales',
            'nearly14days_qty',
            'sales_trend',
            'stocking_coefficient',
            'pr_count',
            'purchase_on_way_num',
            'available_stock_num',
            'actual_stock_num',
            'newwms_use_num',
            'occupy_stock_num',
            'total_stock_num',
            'leave_num',
            'price',
            'order_point',
            'replenishment_num',
            'remark',
            'request_date',
            'compute_batch',
            'planner_nick'
        ];
        return DB::table('mrp_result_plan_v3')->insertUsing($columns, $search);
    }

    //计划单临时表 mrp_tmp_mrp_sku_plan_v3
    private function mrpTmpMrpSkuPlanV3()
    {
        MrpTmpMrpSkuPlanV3::query()->truncate();
        $search = MrpReportStockCountV3::query()->from('mrp_report_stock_count_v3', 'a')->select(
            [
                'sku',
                'stock_way',
                'sales_status',
                'order_day_times_14',
                'day_sales_14',
                'pr_count',
                'pr_no_po',
                'pr_po_no_print',
                'purchase_on_way_num'
                ,
                DB::raw('available_stock available_stock_num')
                ,
                'act_stock',
                'occupy_stock',
                'total_stock',
                'price',
                'order_point',
                'stock_cycle',
                'stock_advance_cycle',
                'a.nearly14days_qty',
                'a.stocking_coefficient',
                'warehouseid',
                'a.sales_trend',
                'newwms_use_qty',
                'fixed_stock_num'
                ,
                DB::raw('ceil(
  case when a.stock_way = 1 and a.total_stock < a.order_point
    then
        a.order_point-a.total_stock
  when a.stock_way = 2 and a.order_day_times_14 = 0 and a.total_stock < a.order_point
    then
        0-a.total_stock + fixed_stock_num
  when a.stock_way = 2 and a.order_day_times_14 >= 1 and a.order_day_times_14 <= 3
    then
      case when a.price >= 100 and a.total_stock < a.order_point
        then
           0-a.total_stock + fixed_stock_num
      when a.price < 100 and a.total_stock < a.order_point
        then
           2-a.total_stock + fixed_stock_num
      else 0
      end
   when a.stock_way = 2 and a.order_day_times_14 > 3 and a.total_stock < a.order_point
     then
      a.stock_cycle * a.nearly14days_qty * a.stocking_coefficient + (a.stock_advance_cycle * a.nearly14days_qty - a.total_stock + fixed_stock_num)
   else 0
   end ) replenishment_num')
                ,
                DB::raw(' case when a.available_stock<= 0 or a.nearly14days_qty = 0
                then
                    curdate()
                else
                    date_add(curdate(), interval floor(a.available_stock/a.nearly14days_qty) day)
                end
                request_date')

            ]
        )->having('replenishment_num', '>', 0);
        $columns = [
            'sku',
            'stock_way',
            'sales_status',
            'order_day_times_14',
            'day_sales_14',
            'pr_count',
            'pr_no_po',
            'pr_po_no_print',
            'purchase_on_way_num',
            'available_stock',
            'act_stock',
            'occupy_stock',
            'total_stock',
            'price',
            'order_point',
            'stock_cycle',
            'stock_advance_cycle',
            'nearly14days_qty',
            'stocking_coefficient',
            'warehouseid',
            'sales_trend',
            'newwms_use_qty',
            'fixed_stock_num',
            'replenishment_num',
            'request_date'
        ];
        return DB::table('mrp_tmp_mrp_sku_plan_v3')->insertUsing($columns, $search);
    }

    /**
     * 进销存：mrp_sku_orders_mod_bef_v3
     * 新表：mrp_report_orders_mod_bef_v3
     * MRP(国内)->MRP V3->SKU日销量统计（修正前）
     */
    public function mrpReportOrdersModBefV3()
    {
        mrpReportOrdersModBefV3::query()->truncate();
        $computeBatch = $this->getComputeBatch();
        $ak = MrpReportOrigSalesdataModV3::query()->select(
            'sku',
            DB::raw('left(payment_date,10) r_date'),
            DB::raw('sum(item_count) item_count')
        )->groupBy(['sku', DB::raw('left(payment_date,10)')]);
        $ake = MrpReportOrigSalesdataModV3::query()->select(
            'sku',
            DB::raw('round(sum((case when payment_date>=date_sub(curdate(),interval 2 day) then item_count else 0 end ))/3,2) nearly3days_qty'),
            DB::raw('round(sum((case when payment_date>=date_sub(curdate(),interval 6 day) then item_count else 0 end ))/7,2) nearly7days_qty'),
            DB::raw('round(sum((case when payment_date>=date_sub(curdate(),interval 13 day) then item_count else 0 end ))/14,2) nearly14days_qty'),
        )->groupBy('sku');
        $search = DB::table(DB::raw("({$ak->toSql()}) as ak"))
            ->joinSub($ake, 'ake', function ($join) {
                $join->on('ak.sku', '=', 'ake.sku');
            })
            ->leftJoin('mrp_base_sku_core as b', function ($join) {
                $join->on('ake.sku', '=', 'b.sku')->where('b.type', 1);
            })
            ->select([
                'ak.sku',
                'b.stock_way',
                'b.sales_status',
                'ake.nearly3days_qty',
                'ake.nearly7days_qty',
                'ake.nearly14days_qty',
                DB::raw('max(case when
                ake.nearly7days_qty >= 0 and ak.item_count/ake.nearly14days_qty >= 3
                    then 1
                else -1 end) as mod_condition'),
                DB::raw('sum(ak.item_count) day_sales_14'),
                DB::raw("'$computeBatch' compute_batch")

            ])
            ->groupBy('ak.sku');
        $columns = ['sku', 'stock_way', 'sales_status', 'nearly3days_qty', 'nearly7days_qty', 'nearly14days_qty', 'mod_condition', 'day_sales_14', 'compute_batch'];
        return DB::table('mrp_report_orders_mod_bef_v3')->insertUsing($columns, $search);
    }
}
