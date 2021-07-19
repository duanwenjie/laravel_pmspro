<?php

namespace App\Models\PrUpload;

use App\Models\MrpBaseData\MrpBaseSkuInfoList;
use App\Services\MrpBaseData\OmsService;
use EloquentFilter\Filterable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PrUploadSkusList extends Model
{
    use HasFactory, Filterable;

    protected $guarded = [];
    protected $appends = [
        'status_name',
        'warehouse_name',
        'check_status_name',
    ];

    // 新系统PR单号起始ID
    public const pmsProPrIdStart = 60000000000;

    // 检测状态
    public const noPass = -1;
    public const pass = 1;
    public const error = 2;

    // PR单状态
    public const cancel = -1;
    public const noHandle = 0;
    public const handle = 1;
    public const unusual = 2;
    public const noHandleNew = 5;
    public const unusualWaitHandle = 7;
    public const waitPurchase = 10;
    public const print = 20;
    public const poCancel = 100;

    // 检测状态描述
    public const checkStatusMap = [
        self::noPass   => '验证不通过',
        self::noHandle => '待处理',
        self::pass     => '验证通过',
        self::error    => '异常信息',
    ];

    // 状态描述
    public const statusMap = [
        self::cancel            => '已撤销',
        self::noHandle          => '待处理',
        self::handle            => '已生成采购单',
        self::unusual           => '异常信息处理',
        self::noHandleNew       => '待处理(新)',
        self::unusualWaitHandle => '异常待处理',
        self::waitPurchase      => '生成采购单待采购',
        self::print             => '生成采购单已打印',
        self::poCancel          => '生成采购单已取消',
    ];


    // PMS采购单明细和PR状态映射
    public const statusShine = [
        PrUploadSkusList::waitPurchase   => ['2,3,4'], // 待采购、审核中、可打印
        PrUploadSkusList::print    => ['5,6,7,9,10,11,12'], // 已打印、未完全到货、完全到货、已质检、未完全入库、完全入库、手动完结
        PrUploadSkusList::poCancel => ['13'], // 取消
    ];

    // PMS采购单明细SKU状态
    public const pmsDetailSkuStatusMap = [
        2  => '待采购',
        3  => '审核中',
        4  => '可打印',
        5  => '已打印',
        6  => '未完全到货',
        7  => '完全到货',
        9  => '已质检',
        10 => '未完全入库',
        11 => '完全入库',
        12 => '手动完结',
        13 => '取消',
    ];

    public const pmsPoDetailStatusShine = [
        2  => self::waitPurchase,
        3  => self::waitPurchase,
        4  => self::waitPurchase,
        5  => self::print,
        6  => self::print,
        7  => self::print,
        9  => self::print,
        10 => self::print,
        11 => self::print,
        12 => self::print,
        13 => self::cancel,
    ];


    public function skuInfo()
    {
        return $this->hasOne(MrpBaseSkuInfoList::class, 'sku', 'sku');
    }

    public function getStatusNameAttribute()
    {
        return self::statusMap[$this->status] ?? '';
    }

    public function getWarehouseNameAttribute()
    {
        return OmsService::warehouseMap[$this->warehouseid] ?? '';
    }

    public function getCheckStatusNameAttribute()
    {
        return self::checkStatusMap[$this->check_status] ?? '';
    }
}
