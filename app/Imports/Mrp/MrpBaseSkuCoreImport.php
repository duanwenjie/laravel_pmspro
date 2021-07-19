<?php

namespace App\Imports\Mrp;

use App\Exceptions\InvalidRequestException;
use App\Imports\BaseImport;
use App\Models\Mrp\MrpBaseSkuCore;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\Rule;
use Maatwebsite\Excel\Concerns\Importable;
use Maatwebsite\Excel\Concerns\ToArray;
use Maatwebsite\Excel\Concerns\WithChunkReading;
use Maatwebsite\Excel\Concerns\WithHeadingRow;

class MrpBaseSkuCoreImport extends BaseImport implements ToArray, WithHeadingRow, WithChunkReading
{
    use Importable;


    protected $header = [
        'SKU'    => 'sku',
        '备货方式'   => 'stock_way',
        '销售状态'   => 'sales_status',
        '安全库存天数' => 'buffer_stock_cycle',
        '交期'     => 'supply_cycle',
        '补货天数'   => 'stock_cycle',
        '活动库存天数' => 'active_stock_cycle',
        '特定备货数量' => 'fixed_stock_num',
        '备注'     => 'remark',
        '产品标识'   => 'sku_mark'
    ];


    protected $type;

    public function __construct($type)
    {
        parent::__construct();
        $this->type = $type;
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
            '*.sku'                => 'required',
            '*.stock_way'          => ['required', Rule::in(array_values(MrpBaseSkuCore::$stockWay))],
            '*.sales_status'       => ['required', Rule::in(array_values(MrpBaseSkuCore::$salesStatus))],
            '*.buffer_stock_cycle' => 'required',
            '*.supply_cycle'       => 'required',
            '*.stock_cycle'        => 'required',
            '*.active_stock_cycle' => 'required',
            '*.fixed_stock_num'    => 'required',
            '*.sku_mark'           => 'required',
        ], [], [
            '*.sku'                => 'SKU',
            '*.stock_way'          => '备货方式',
            '*.sales_status'       => '销售状态',
            '*.buffer_stock_cycle' => '安全库存天数',
            '*.supply_cycle'       => '交期',
            '*.stock_cycle'        => '补货天数',
            '*.active_stock_cycle' => '活动库存天数',
            '*.fixed_stock_num'    => '特定备货数量',
            '*.sku_mark'           => '产品标识',
        ]);

        if ($validator->fails()) {
            throw new InvalidRequestException(join('|', $validator->errors()->all()));
        }
        $status = array_flip(MrpBaseSkuCore::$salesStatus);
        $stockWay = array_flip(MrpBaseSkuCore::$stockWay);
        $temp = [];
        foreach ($rows as $row) {
            $row['stock_way'] = $stockWay[$row['stock_way']];
            $row['sales_status'] = $status[$row['sales_status']];
            $row['type'] = $this->type;
            $row['created_user'] = Auth::user()->nickname;
            $temp[] = $row;
        }

        MrpBaseSkuCore::query()->upsert($temp, ['sku', 'type']);
    }

    public function chunkSize(): int
    {
        return 1000;
    }
}
