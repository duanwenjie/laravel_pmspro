<?php
/**
 * notes
 * User: jiangshilin(jiangshilin@youkeshu.com)
 * Date: 2021/7/2
 * Time: 18:10
 */


namespace App\Models\Pms;


use EloquentFilter\Filterable;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class CancelPoBtDetail extends Model
{
    use HasFactory, Filterable;
    protected $table = 'cancel_po_bt_detail';
    public $timestamps = false;
}
