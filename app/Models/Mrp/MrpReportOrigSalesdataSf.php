<?php

namespace App\Models\Mrp;

use EloquentFilter\Filterable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class MrpReportOrigSalesdataSf extends Model
{
    use HasFactory, Filterable;

    protected $table = 'mrp_report_orig_salesdata_sf';
}
