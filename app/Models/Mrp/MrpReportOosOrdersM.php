<?php

namespace App\Models\Mrp;

use App\Models\Model;
use EloquentFilter\Filterable;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class MrpReportOosOrdersM extends Model
{
    use HasFactory, Filterable;

    protected $table = 'mrp_report_oos_orders_m';
}
