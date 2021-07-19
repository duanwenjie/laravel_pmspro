<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class CurlApiLog extends Model
{
    use HasFactory;

    protected $guarded = [];

    public const receiveWmsStockType = 1;
    public const receivePmsPrType = 2;
    public const pushPmsPrType = 3;

    public const success = 1;
    public const error = 0;

    // 类型描述
    public const typeMap = [
        self::receiveWmsStockType => '接收WMS系统SKU库存数据',
        self::receivePmsPrType    => '接收PMS系统PR单数据',
        self::pushPmsPrType       => '推送PMS系统PR单数据',
    ];

    // 状态描述
    public const statusMap = [
        self::success => '接口请求成功',
        self::error   => '接口请求失败',
    ];
}
