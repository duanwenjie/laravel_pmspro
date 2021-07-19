<?php

namespace App\Models\Mrp;

use App\Models\Model;
use EloquentFilter\Filterable;
use Illuminate\Database\Eloquent\Factories\HasFactory;

//MRP(国内)-》平台+SKU销量统计(不剔除)
class MrpReportSalesCountPlatformAll extends Model
{
    use HasFactory, Filterable;

    protected $table = 'mrp_report_sales_count_platform_all';
}
