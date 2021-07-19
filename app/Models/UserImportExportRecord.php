<?php

namespace App\Models;

use EloquentFilter\Filterable;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class UserImportExportRecord extends Model
{
    use HasFactory, Filterable;

    const STATUS_NORMAL = 1;
    const STATUS_PENDING = 2;
    const STATUS_SUCCESS = 3;
    const STATUS_FAIL = 4;
    const STATUS_PART_FAIL = 5;
    const TYPE_EXPORT = 1;
    const TYPE_IMPORT = 2;
    const MODULE_USER = 'user_module';
    const MODULE_GN_MRP = 'mrp_module';
    const MODULE_GN_PR_BATCH_UPLOAD = 'pr_batch_upload_module';
    public static $statusMap = [
        self::STATUS_NORMAL    => '待处理',
        self::STATUS_PENDING   => '处理中',
        self::STATUS_SUCCESS   => '处理成功',
        self::STATUS_FAIL      => '处理失败',
        self::STATUS_PART_FAIL => '部分失败',
    ];

    //module定义
    public static $typesMap = [
        self::TYPE_EXPORT => '导出',
        self::TYPE_IMPORT => '导入',
    ]; //用户模块
    public static $moduleMap = [
        self::MODULE_USER               => '用户模块',
        self::MODULE_GN_MRP             => '国内MRP',
        self::MODULE_GN_PR_BATCH_UPLOAD => '国内PR上传',
    ]; //国内MRP
    public $appends = ['status_desc', 'type_desc', 'module_desc']; //国内PR上传
    protected $guarded = [];

    public function getStatusDescAttribute()
    {
        return self::$statusMap[$this->status] ?? '';
    }

    public function getTypeDescAttribute()
    {
        return self::$typesMap[$this->type] ?? '';
    }

    public function getModuleDescAttribute()
    {
        return self::$moduleMap[$this->module] ?? '';
    }
}
