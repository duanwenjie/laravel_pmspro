<?php

namespace App\Models\Mrp;

use App\Models\Model;
use EloquentFilter\Filterable;
use Illuminate\Database\Eloquent\Factories\HasFactory;

/**
 * Class MrpReportOrdersModAftV3
 * @package App\Models
 */
class MrpReportOrdersModAftV3 extends Model
{
    use HasFactory, Filterable;


    protected $table = 'mrp_report_orders_mod_aft_v3';
    public $appends = ['sales_trend_desc'];
    public $timestamps = false;
    protected $guarded = [];

    const SALES_TREND_SMOOTH = 0;
    const SALES_TREND_RISE = 1;
    const SALES_TREND_DECLINE = -1;

    public static $salesTrendMap = [
        self::SALES_TREND_SMOOTH  => '平稳',
        self::SALES_TREND_RISE    => '上涨',
        self::SALES_TREND_DECLINE => '下降',
    ];


    /**
     * 获取 销量趋势
     * @return string
     */
    public function getSalesTrendDescAttribute()
    {
        return self::$salesTrendMap[$this->sales_trend] ?? '';
    }
}
