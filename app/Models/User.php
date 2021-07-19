<?php

namespace App\Models;

use EloquentFilter\Filterable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

class User extends Authenticatable
{
    use HasFactory, Notifiable, Filterable;

    public $appends = ['status_desc'];

    const STATUS_IN = 1;
    const STATUS_LEAVE = 2;

    public static $statusMap = [
        self::STATUS_IN    => '在职',
        self::STATUS_LEAVE => '离职',
    ];


    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $guarded = [

    ];

    /**
     * Desc:导出记录的日志列表
     * @return HasMany
     */
    public function importExportRecords()
    {
        return $this->hasMany(UserImportExportRecord::class, 'user_id', 'id');
    }

    public function getStatusDescAttribute()
    {
        return self::$statusMap[$this->status] ?? '';
    }
}
