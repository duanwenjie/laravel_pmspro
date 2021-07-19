<?php
/**
 * notes
 * User: jiangshilin(jiangshilin@youkeshu.com)
 * Date: 2021/5/22
 * Time: 16:08
 */


namespace App\Models\MrpBaseData;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class MrpTmpMrpSkuPlanV3 extends Model
{
    use HasFactory;

    protected $guarded = [];
    protected $table = 'mrp_tmp_mrp_sku_plan_v3';
    public $timestamps = false;
}
