<?php


namespace App\Services\MrpBaseData;

use App\Models\MrpBaseData\MrpBaseOmsSalesList;
use Carbon\Carbon;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\DB;

class ReportService
{
    //v3版本需要排除的平台'B2B','xshoppy','new_shopify','shopyy','shoplazza','shopline'
    const delPtCodeV3 = [
        'XX',
        'XP',
        'SF',
        'SY',
        'SZ',
        'SL'
    ];
    // 数据原排除已下订单状态
    protected const omsStatusMap = [
        4  => '已取消',
        5  => '已撤单',
        6  => '待审核',
        7  => '未发货且已推送',
        9  => '平台撤单',
        15 => '已删除',
        18 => '已退件',
    ];

    /**
     * Desc:获取某一时间段内的销量 或者 某一时间段的销量
     * @param $skuList
     * @param $days
     * @param $type
     * @param  bool  $range
     * @return array
     */
    public function getSaleInfoByDays($skuList, $days, $type, $range = false)
    {
        $days = Arr::sort($days);
        //根据不同的type取不同的平台
        $endDay = end($days);
        $minDay = Carbon::now()->subDays($endDay)->format('Y-m-d');
        $selectFields = ['sku'];
        $emptyResult = [];
        foreach ($days as $day) {
            $emptyResult["nearly{$day}days_qty"] = 0;
        }
        $selectFields = array_merge($selectFields, $this->getDateColumnsByDays($days, $range));
        $list = $this->getSaleInfoByDaysBuild($type, $minDay, $selectFields)->whereIn(
            'sku',
            $skuList
        )->get()->toArray();
        $list = array_column($list, null, 'sku');
        foreach ($skuList as $sku) {
            if (!isset($list[$sku])) {
                $list[$sku] = $emptyResult;
            }
        }
        return $list;
    }

    public function getDateColumnsByDays($days, $range = false)
    {
        $days = Arr::sort($days);
        $selectFields = [];
        foreach ($days as $day) {
            $carbonDay = Carbon::now()->subDays($day)->format('Y-m-d');
            if ($range) {
                $selectFields[] = DB::raw("sum(case when left(payment_date,10) >= '{$carbonDay}' then item_count else 0 end) nearly{$day}days_qty");
            } else {
                $selectFields[] = DB::raw("sum(case when left(payment_date,10) = '{$carbonDay}' then item_count else 0 end) nearly{$day}days_qty");
            }
        }
        return $selectFields;
    }

    public function getSaleInfoByDaysBuild($type, $minDay, $selectFields)
    {
        $build = MrpBaseOmsSalesList::query()
            ->whereNotIn('order_status', array_keys(self::omsStatusMap))
            ->where('payment_date', '>=', $minDay);
//        如果是SF 只取独立站的销量 其他排除
//        if ($type == MrpBaseSkuCore::TYPE_SF) {
//            $build->whereIn('platform_code', OmsService::independentMap);
//        } else {
//            $build->whereNotIn('platform_code', self::delPtCodeV3);
//        }
        return $build->select($selectFields)
            ->groupBy('sku');
    }

    public function getSaleInfoByDaysSql($days, $type, $range = false)
    {
        $days = Arr::sort($days);
        $endDay = end($days);
        $minDay = Carbon::now()->subDays($endDay)->format('Y-m-d');
        $selectFields = ['sku'];
        $selectFields = array_merge($selectFields, $this->getDateColumnsByDays($days, $range));
        return $this->getSaleInfoByDaysBuild($type, $minDay, $selectFields);
    }

    //通过天获取付款时间查询列

    public function getComputeBatch()
    {
        if (date('H') < 12) {
            return date('Y-m-d').' 06:00:00';
        } else {
            return date('Y-m-d').' 13:00:00';
        }
    }

    //通过天获取insert付款时间查询列

    public function getInsertDateColumnsByDays($days, $columnsPrefix = 'old_day_sales_',$sumKey='item_count')
    {
        $days = Arr::sort($days);
        $selectFields = [];
        foreach ($days as $day) {
            $carbonDay = Carbon::now()->subDays($day - 1)->format('Y-m-d');
            $selectFields["{$columnsPrefix}{$day}"] = DB::raw("sum(case when left(payment_date,10) = '{$carbonDay}' then {$sumKey} else 0 end) {$columnsPrefix}{$day}");
        }
        return $selectFields;
    }
}
