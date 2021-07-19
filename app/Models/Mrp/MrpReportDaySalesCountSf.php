<?php

namespace App\Models\Mrp;

use EloquentFilter\Filterable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class MrpReportDaySalesCountSf extends Model
{
    use HasFactory, Filterable;

    protected $table = 'mrp_report_day_sales_count_sf';

    public $appends = ['stock_way_name', 'sales_status_name'];

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

    public function getStockWayNameAttribute()
    {
        return self::$stockWay[$this->stock_way] ?? '';
    }

    public function getSalesStatusNameAttribute()
    {
        return self::$salesStatus[$this->sales_status] ?? '';
    }
}
