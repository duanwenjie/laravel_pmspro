<?php

namespace App\Imports\PrUpload;

use App\Exceptions\InvalidRequestException;
use App\Imports\BaseImport;
use App\Services\PrUpload\PoBatchUploadService;
use Illuminate\Support\Facades\Validator;
use Maatwebsite\Excel\Concerns\Importable;
use Maatwebsite\Excel\Concerns\ToArray;
use Maatwebsite\Excel\Concerns\WithHeadingRow;

class PoBatchUploadImportSave extends BaseImport implements ToArray, WithHeadingRow
{
    use Importable;
    protected $header = [
        'PR单号'  => 'id',
        '下单数'   => 'quantity',
        '备注'    => 'remark',
        '未下单原因' => 'no_order_reason',
    ];

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
        if (empty($rows)){
            throw new InvalidRequestException('导入文件内容不能为空，请核对！');
        }
        $this->validateHeader($rows[0]);

        $validator = Validator::make($rows, [
            '*.id'          => 'required',
            '*.quantity'     => 'integer|numeric|min:1',
        ], [], [
            '*.id'          => 'PR单',
            '*.quantity'    => '下单数',
        ]);

        if ($validator->fails()) {
            throw new InvalidRequestException(join('|', $validator->errors()->all()));
        }

        PoBatchUploadService::handleImportSaveData($rows);
    }
}
