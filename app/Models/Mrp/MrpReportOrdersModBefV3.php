<?php

namespace App\Models\Mrp;

use App\Models\Model;
use EloquentFilter\Filterable;
use Illuminate\Database\Eloquent\Factories\HasFactory;

/**
 * Class MrpReportOrdersModBefV3
 * @package App\Models
 */
class MrpReportOrdersModBefV3 extends Model
{
    use HasFactory, Filterable;


    protected $table = 'mrp_report_orders_mod_bef_v3';
    public $appends = ['sales_status_desc', 'stock_way_desc', 'mod_condition_desc'];
    public $timestamps = false;
    protected $guarded = [];

    const STOCK_ISSUE = 1;
    const STOCK_SALE = 2;
    const STOCK_SUPPLIER_UP = 3;
    const STOCK_SUPPLIER_SEND = 4;
    const STOCK_SUPPLIER_COLLECTION = 5;

    public static $stockWayMap = [
        self::STOCK_ISSUE               => '出单备货',
        self::STOCK_SALE                => '备货销售',
        self::STOCK_SUPPLIER_UP         => '供方直上',
        self::STOCK_SUPPLIER_SEND       => '供方寄售',
        self::STOCK_SUPPLIER_COLLECTION => '供方集采',
    ];

    const STATUS_NEW_PRO = 1;
    const STATUS_SALE_PRO = 2;
    const STATUS_CLEAR_PRO = 3;
    const STATUS_OFF_PRO = 4;
    public static $salesStatusMap = [
        self::STATUS_NEW_PRO   => '新品',
        self::STATUS_SALE_PRO  => '在售品',
        self::STATUS_CLEAR_PRO => '清仓品',
        self::STATUS_OFF_PRO   => '下架品',
    ];
    const TRIGGER = 1;
    const NO_TRIGGER = -1;
    public static $modConditionMap = [
        self::TRIGGER    => '触发',
        self::NO_TRIGGER => '不触发',
    ];


    /**
     * 获取 销售状态
     * @return string
     */
    public function getSalesStatusDescAttribute()
    {
        return self::$salesStatusMap[$this->sales_status] ?? '';
    }

    /**
     * 获取 备货方式
     * @return string
     */
    public function getStockWayDescAttribute()
    {
        return self::$stockWayMap[$this->stock_way] ?? '';
    }

    /**
     * 获取 是否触发销量调整
     * @return string
     */
    public function getModConditionDescAttribute()
    {
        return self::$modConditionMap[$this->mod_condition] ?? '';
    }
}
