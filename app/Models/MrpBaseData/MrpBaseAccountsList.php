<?php

namespace App\Models\MrpBaseData;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class MrpBaseAccountsList extends Model
{
    use HasFactory;

    protected $guarded = [];

    public function platForm()
    {
        return $this->hasOne('App\Models\MrpBaseData\MrpBasePlatformList', 'platform_code', 'platform_code');
    }
}
