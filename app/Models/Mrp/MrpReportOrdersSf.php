<?php

namespace App\Models\Mrp;

use EloquentFilter\Filterable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class MrpReportOrdersSf extends Model
{
    use HasFactory, Filterable;

    protected $table = 'mrp_report_orders_sf';

    public $appends = ['stock_way_name', 'sales_status_name'];


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
        return MrpBaseSkuCore::$stockWay[$this->stock_way] ?? '';
    }

    public function getSalesStatusNameAttribute()
    {
        return self::$salesStatus[$this->sales_status] ?? '';
    }

    public function getSalesTrendDesAttribute()
    {
        return self::$salesTrend[$this->sales_trend] ?? '';
    }
}
