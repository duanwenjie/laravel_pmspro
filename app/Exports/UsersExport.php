<?php

namespace App\Exports;

use App\Exports\Mrp\BaseExport;
use Maatwebsite\Excel\Concerns\FromQuery;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;

class UsersExport extends BaseExport implements FromQuery, ShouldAutoSize, WithHeadings, WithMapping
{
    protected $builder;

    public function __construct($builder)
    {
        $this->builder = $builder;
    }

    public function query()
    {
        return $this->builder;
    }

    public function headings(): array
    {
        return ['用户名', '姓名', '状态', '同步时间'];
    }

    public function map($user): array
    {
        return [
            $user->username,
            $user->nickname,
            $user->status,
            $user->updated_at,
        ];
    }
}
