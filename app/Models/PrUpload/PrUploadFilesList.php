<?php

namespace App\Models\PrUpload;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PrUploadFilesList extends Model
{
    use HasFactory;

    protected $guarded = [];
    // 文件状态
    public const noPass = -1;
    public const waitCheck = 0;
    public const partPass = 1;
    public const pass = 2;

    // 状态描述
    public const checkStatusMap = [
        self::noPass    => '数据检验未通过',
        self::waitCheck => '未检验',
        self::partPass  => '部分检验通过',
        self::pass      => '完全检验通过',
    ];
}
