<?php
/**
 * notes
 * User: jiangshilin(jiangshilin@youkeshu.com)
 * Date: 2021/7/1
 * Time: 18:03
 */


namespace App\Models\Pms;


use EloquentFilter\Filterable;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Purchaseorder extends Model
{
    use HasFactory, Filterable;
    protected  $table = 'purchaseorder';
}
