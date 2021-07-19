<?php
/**
 * notes
 * User: jiangshilin(jiangshilin@youkeshu.com)
 * Date: 2021/6/30
 * Time: 18:35
 */


namespace App\Models\Pms;


use EloquentFilter\Filterable;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Purchaseplan extends Model
{
    use HasFactory, Filterable;
    protected  $table = 'purchaseplan';
}
