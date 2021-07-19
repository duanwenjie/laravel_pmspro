<?php

namespace App\Models\Mrp;

use App\Models\Model;
use EloquentFilter\Filterable;
use Illuminate\Database\Eloquent\Factories\HasFactory;

//销量源数据
class MrpReportOrigSalesdataV3 extends Model
{
    use HasFactory, Filterable;

    protected $table = 'mrp_report_orig_salesdata_v3';
    public $timestamps = false;
}
