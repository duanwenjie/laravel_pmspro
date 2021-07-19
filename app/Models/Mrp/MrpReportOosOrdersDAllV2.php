<?php

namespace App\Models\Mrp;

use App\Models\Model;
use EloquentFilter\Filterable;
use Illuminate\Database\Eloquent\Factories\HasFactory;

//MRP(国内)-》撤单和缺货订单日统计
class MrpReportOosOrdersDAllV2 extends Model
{
    use HasFactory, Filterable;

    protected $table = 'mrp_report_oos_orders_d_all_v2';
}
