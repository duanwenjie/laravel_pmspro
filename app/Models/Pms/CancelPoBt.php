<?php
/**
 * notes
 * User: jiangshilin(jiangshilin@youkeshu.com)
 * Date: 2021/7/3
 * Time: 10:32
 */


namespace App\Models\Pms;


use EloquentFilter\Filterable;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class CancelPoBt extends Model
{
    use HasFactory, Filterable;
    protected $table = 'cancel_po_bt';
    public $timestamps = false;
}
