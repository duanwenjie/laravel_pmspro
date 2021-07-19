<?php
/**
 * notes
 * User: jiangshilin(jiangshilin@youkeshu.com)
 * Date: 2021/7/3
 * Time: 9:05
 */


namespace App\Imports\Mrp;


use App\Exceptions\InvalidRequestException;
use App\Imports\BaseImport;
use App\Models\Pms\CancelPoBtDetail;
use Illuminate\Support\Facades\Validator;
use Maatwebsite\Excel\Concerns\Importable;
use Maatwebsite\Excel\Concerns\ToArray;
use Maatwebsite\Excel\Concerns\WithChunkReading;
use Maatwebsite\Excel\Concerns\WithHeadingRow;

class CancelpoImport extends BaseImport implements ToArray, WithHeadingRow, WithChunkReading
{
    use Importable;


    protected $header = [
        'BATCH NUMBER' => 'bt_no',
        'PO' => 'po_id',
        'SKU' => 'sku',
    ];

    /**
     * Desc:
     * @param array $rows
     * @throws InvalidRequestException
     */
    public function array(array $rows)
    {
        $this->validateHeader($rows[0]);
        $validator = Validator::make($rows, [
            '*.bt_no' => 'required',
            '*.po_id' => 'required',
            '*.sku' => 'required',
        ], [], [
            '*.bt_no' => 'BATCH NUMBER',
            '*.po_id' => 'PO',
            '*.sku' => 'SKU',
        ]);
        if ($validator->fails()) {
            throw new InvalidRequestException(join('|', $validator->errors()->all()));
        }
        $temp = [];
        foreach ($rows as $row) {
            $row['is_new'] = $row['is_show'] = 1;
            $temp[] = $row;
        }
        CancelPoBtDetail::query()->upsert($temp, ['is_new','is_show','sku']);
    }

    public function chunkSize(): int
    {
        return 1000;
    }
}
