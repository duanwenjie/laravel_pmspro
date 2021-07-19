<?php

namespace App\Models\Mrp;

use App\Models\Model;
use EloquentFilter\Filterable;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class MrpReportOrdersModBefDetailV3 extends Model
{
    use HasFactory, Filterable;

    protected $table = 'mrp_report_orders_mod_bef_detail_v3';
}
