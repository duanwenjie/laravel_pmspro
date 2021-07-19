<?php

namespace App\Models\Mrp;

use App\Models\Model;
use EloquentFilter\Filterable;
use Illuminate\Database\Eloquent\Factories\HasFactory;

//MRP(国内)-》总缺货订单明细
class MrpReportOosOrdersDetailTotal extends Model
{
    use HasFactory, Filterable;

    protected $table = 'mrp_report_oos_orders_detail_total';
}
