<?php

namespace App\Imports;

use App\Exceptions\InvalidRequestException;
use Maatwebsite\Excel\Imports\HeadingRowFormatter;

class BaseImport
{
    public function __construct()
    {
        HeadingRowFormatter::extend('customHeader', function ($value) {
            return $this->header[$value] ?? $value;
        });
        HeadingRowFormatter::default('customHeader');
    }


    public function validateHeader($row)
    {
        if (array_diff(array_values($this->header), array_keys($row))) {
            throw new InvalidRequestException('导入文件标题不正确');
        }
    }
}
