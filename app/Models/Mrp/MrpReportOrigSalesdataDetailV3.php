<?php

namespace App\Models\Mrp;

use App\Models\Model;
use EloquentFilter\Filterable;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class MrpReportOrigSalesdataDetailV3 extends Model
{
    use HasFactory, Filterable;

    protected $table = 'mrp_report_orig_salesdata_detail_v3';
}
