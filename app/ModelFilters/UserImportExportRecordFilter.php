<?php


namespace App\ModelFilters;

use App\Tools\Formater;
use EloquentFilter\ModelFilter;

class UserImportExportRecordFilter extends ModelFilter
{
    public function status($value)
    {
        $this->where('status', $value);
    }

    public function type($value)
    {
        $this->where('type', $value);
    }

    public function module($value)
    {
        $this->where('module', $value);
    }

    public function createdAt($value)
    {
        //前端返回时间
        $value = Formater::searchTime($value);
        if (!empty($value[0]) && empty($value[1])) {
            $this->where('created_at', '>', $value[0]);
        }
        if (!empty($value[1]) && empty($value[0])) {
            $this->where('created_at', '<', $value[1]);
        }
        if (!empty($value[0]) && !empty($value[1])) {
            $this->whereBetween('created_at', $value);
        }
    }
}
