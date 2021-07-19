<?php

namespace App\Models\Mrp;

use App\Models\Model;
use EloquentFilter\Filterable;
use Illuminate\Database\Eloquent\Factories\HasFactory;

/*MRP(国内)-》MRP V3-》计算SKU自动补货*/

class MrpResultPlanV3 extends Model
{
    use HasFactory, Filterable;

    protected $table = 'mrp_result_plan_v3';

    public $appends = ['stock_way_name', 'sales_status_name', 'confirm_status_name'];

    const STOCK_WAY_ORDER_STOCKING = 1;
    const STOCK_WAY_STOCK_SALE = 2;
    const STOCK_WAY_SUPPLIER_STRAIGHT_UP = 3;
    const STOCK_WAY_SUPPLIER_CONSIGNMENT = 4;
    const STOCK_WAY_SUPPLY_SIDE = 5;

    public static $stockWay = [
        self::STOCK_WAY_ORDER_STOCKING       => '出单备货',
        self::STOCK_WAY_STOCK_SALE           => '备货销售',
        self::STOCK_WAY_SUPPLIER_STRAIGHT_UP => '供方直上',
        self::STOCK_WAY_SUPPLIER_CONSIGNMENT => '供方寄售',
        self::STOCK_WAY_SUPPLY_SIDE          => '供方集采',
    ];

    const SALES_STATUS_NEW = 1;
    const SALES_STATUS_ON_SALE = 2;
    const SALES_STATUS_CLEARANCE_ITEMS = 3;
    const SALES_STATUS_OFF_SHELF = 4;

    public static $salesStatus = [
        self::SALES_STATUS_NEW             => '新品',
        self::SALES_STATUS_ON_SALE         => '在售品',
        self::SALES_STATUS_CLEARANCE_ITEMS => '清仓品',
        self::SALES_STATUS_OFF_SHELF       => '下架品',
    ];

    const CONFIRM_STATUS_TO_BE_CONFIRMED = 1;
    const CONFIRM_STATUS_CONFIRMED = 2;
    const CONFIRM_STATUS_CANCELLED = -1;
    const CONFIRM_STATUS_SYSTEM_CANCELLATION = -2;

    public static $confirmStatus = [
        self::CONFIRM_STATUS_TO_BE_CONFIRMED     => '待确认',
        self::CONFIRM_STATUS_CONFIRMED           => '已确认',
        self::CONFIRM_STATUS_CANCELLED           => '已取消',
        self::CONFIRM_STATUS_SYSTEM_CANCELLATION => '系统撤销',
    ];

    const SALES_TREND_UNKNOWN = -1;
    const SALES_TREND_NODATA = 0;
    const SALES_TREND_STEADY_RISE = 1;
    const SALES_TREND_RAPID_RISE = 2;
    const SALES_TREND_SKYROCKET = 3;
    const SALES_TREND_CONTINUOUS_DECLINE = 4;
    const SALES_TREND_PLUMMET = 5;

    public static $salesTrend = [
        self::SALES_TREND_UNKNOWN            => '趋势不明',
        self::SALES_TREND_NODATA             => '无趋势',
        self::SALES_TREND_STEADY_RISE        => '平稳上涨',
        self::SALES_TREND_RAPID_RISE         => '快速上涨',
        self::SALES_TREND_SKYROCKET          => '暴涨',
        self::SALES_TREND_CONTINUOUS_DECLINE => '连续下滑',
        self::SALES_TREND_PLUMMET            => '暴跌',
    ];

    public function getStockWayNameAttribute()
    {
        return self::$stockWay[$this->stock_way] ?? '';
    }

    public function getSalesStatusNameAttribute()
    {
        return self::$salesStatus[$this->sales_status] ?? '';
    }


    public function getConfirmStatusNameAttribute()
    {
        return self::$confirmStatus[$this->confirm_status] ?? '';
    }

    public function getSalesTrendDesAttribute()
    {
        return self::$salesTrend[$this->sales_trend] ?? '';
    }

    public function skuInfo()
    {
        return $this->hasOne('App\Models\MrpBaseData\MrpBaseSkuInfoList', 'sku', 'sku');
    }

    public function skuCore()
    {
        return $this->hasOne('App\Models\Mrp\MrpBaseSkuCore', 'sku', 'sku')->where(
            'type',
            '=',
            MrpBaseSkuCore::TYPE_V3
        );
    }
}
