<?php
/**
 * notes
 * User: jiangshilin(jiangshilin@youkeshu.com)
 * Date: 2021/5/24
 * Time: 19:07
 */


namespace App\Models\MrpBaseData;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class MrpTmpOmsSalesLists extends Model
{
    use HasFactory;

    protected $guarded = [];
    protected $table = 'mrp_tmp_oms_sales_lists';
    public $timestamps = false;
}
