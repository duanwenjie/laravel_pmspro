<?php

namespace App\Models\Mrp;

use EloquentFilter\Filterable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class MrpBaseSkuCore extends Model
{
    use HasFactory, Filterable;

    protected $table = 'mrp_base_sku_core';

    public $appends = ['stock_way_name', 'sales_status_name', 'sku_price'];

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

    const TYPE_V3 = 1;
    const TYPE_SF = 2;


    public static $type = [
        self::TYPE_V3 => 'V3版本',
        self::TYPE_SF => '海狮版本',
    ];

    public function getStockWayNameAttribute()
    {
        return self::$stockWay[$this->stock_way] ?? '';
    }

    public function getSalesStatusNameAttribute()
    {
        return self::$salesStatus[$this->sales_status] ?? '';
    }

    public function skuInfo()
    {
        return $this->hasOne('App\Models\MrpBaseData\MrpBaseSkuInfoList', 'sku', 'sku');
    }

    public function getSkuPriceAttribute()
    {
        if(!isset($this->skuInfo)) return  0;
        if ($this->skuInfo['last_war_price'] > 0) {
            return $this->skuInfo->last_war_price;
        } else {
            if ($this->skuInfo['supplier_min_price'] > 0) {
                return $this->skuInfo->supplier_min_price;
            } else {
                return $this->skuInfo['price'];
            }
        }
    }
}
