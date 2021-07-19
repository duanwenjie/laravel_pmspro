<?php

namespace App\Models\Mrp;

use App\Models\Model;
use EloquentFilter\Filterable;
use Illuminate\Database\Eloquent\Factories\HasFactory;

//MRP(国内)-》每日缺货订单明细
class MrpReportOosOrdersDetailDaily extends Model
{
    use HasFactory, Filterable;

    protected $table = 'mrp_report_oos_orders_detail_daily';
}
