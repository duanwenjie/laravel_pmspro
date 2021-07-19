<?php

namespace App\Models\Mrp;

use App\Models\Model;
use EloquentFilter\Filterable;
use Illuminate\Database\Eloquent\Factories\HasFactory;

//MRP(国内)-》每日最新缺货占比统计报表
class MrpReportOosOrdersDV2 extends Model
{
    use HasFactory, Filterable;

    protected $table = 'mrp_report_oos_orders_d_v2';
}
