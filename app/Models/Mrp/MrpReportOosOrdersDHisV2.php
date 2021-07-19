<?php

namespace App\Models\Mrp;

use App\Models\Model;
use EloquentFilter\Filterable;
use Illuminate\Database\Eloquent\Factories\HasFactory;

//MRP(国内)-》历史每日缺货占比统计表
class MrpReportOosOrdersDHisV2 extends Model
{
    use HasFactory, Filterable;

    protected $table = 'mrp_report_oos_orders_d_his_v2';
}
