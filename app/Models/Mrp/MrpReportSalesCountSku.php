<?php

namespace App\Models\Mrp;

use App\Models\Model;
use EloquentFilter\Filterable;
use Illuminate\Database\Eloquent\Factories\HasFactory;

//MRP(国内)-》MRP V3-》销量-SKU统计
class MrpReportSalesCountSku extends Model
{
    use HasFactory, Filterable;

    protected $table = 'mrp_report_sales_count_sku';
}
