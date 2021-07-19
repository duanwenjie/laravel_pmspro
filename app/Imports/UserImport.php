<?php

namespace App\Imports;

use App\Exceptions\InvalidRequestException;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Validator;
use Maatwebsite\Excel\Concerns\Importable;
use Maatwebsite\Excel\Concerns\ToArray;
use Maatwebsite\Excel\Concerns\WithHeadingRow;

class UserImport extends BaseImport implements ToArray, WithHeadingRow
{
    use Importable;

    public $header = ['用户名' => 'username', '姓名' => 'nickname'];

    public function __construct()
    {
        parent::__construct();
    }


    /**
     * Desc:
     * @param  array  $rows
     * @throws InvalidRequestException
     */
    public function array(array $rows)
    {
        $this->validateHeader($rows[0]);
        $validator = Validator::make($rows, [
            '*.username' => 'required',
            '*.nickname' => 'required',
        ], [], [
            '*.username' => '用户名',
            '*.nickname' => '姓名',
        ]);
        if ($validator->fails()) {
            throw new InvalidRequestException(join('|', $validator->errors()->all()));
        }
        Log::info('表格导入的数据', $rows);
    }
}
