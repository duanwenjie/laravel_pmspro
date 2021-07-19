<?php

namespace App\Models\PrUpload;

use App\Http\ConfigBase\ConfigBase;
use EloquentFilter\Filterable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PrUploadSkuFollowList extends Model
{
    use HasFactory, Filterable;

    protected $guarded = [];

    protected $appends = [
        'sku_sales_status_name',
        'pms_po_detail_status_name',
        'pr_status_name',
    ];

    public function getSkuSalesStatusNameAttribute()
    {
        return ConfigBase::getSkuSalesStatusMap()[$this->sku_sales_status] ?? '';
    }

    public function getPmsPoDetailStatusNameAttribute()
    {
        return ConfigBase::getPmsPoDetailStatusMap()[$this->pms_po_detail_status] ?? '';
    }

    public function getPrStatusNameAttribute()
    {
        return PrUploadSkusList::statusMap[$this->pr_status] ?? '';
    }
}
