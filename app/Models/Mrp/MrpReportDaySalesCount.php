<?php

namespace App\Models\Mrp;

use App\Models\Model;
use EloquentFilter\Filterable;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class MrpReportDaySalesCount extends Model
{
    use HasFactory, Filterable;

    protected $table = 'mrp_report_day_sales_count';
}
