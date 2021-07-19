<?php

namespace App\Imports\PrUpload;

use App\Exceptions\InvalidRequestException;
use App\Imports\BaseImport;
use App\Services\PrUpload\PoBatchUploadService;
use Carbon\Carbon;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\Rule;
use Maatwebsite\Excel\Concerns\Importable;
use Maatwebsite\Excel\Concerns\ToArray;
use Maatwebsite\Excel\Concerns\WithHeadingRow;
use PhpOffice\PhpSpreadsheet\Shared\Date;

class PoBatchUploadImport extends BaseImport implements ToArray, WithHeadingRow
{
    use Importable;

    public const warehouseId = [3]; // 允许上传的仓别

    protected $header = [
        'sku*'  => 'sku',
        '下单数量*' => 'quantity',
        '仓别*'   => 'warehouse_id',
        '备注'    => 'remark',
        '需求时间'  => 'require_date',
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
        $this->validateHeader($rows[0]);

        $validator = Validator::make($rows, [
            '*.sku'          => 'required',
            '*.quantity'     => 'required|numeric|min:1',
            '*.warehouse_id' => ['required', Rule::in(self::warehouseId)],
        ], [], [
            '*.sku'          => 'SKU',
            '*.quantity'     => '下单数量',
            '*.warehouse_id' => '仓别',
        ]);
        if ($validator->fails()) {
            throw new InvalidRequestException(join('|', $validator->errors()->all()));
        }

        foreach ($rows as &$row) {
            $row['require_date'] = Carbon::instance(Date::excelToDateTimeObject($row['require_date']))->format('Y-m-d');
        }
        PoBatchUploadService::handleImportData($rows);
    }
}
