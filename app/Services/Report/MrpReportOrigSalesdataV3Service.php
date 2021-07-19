<?php


namespace App\Services\Report;

use App\Exceptions\InvalidRequestException;
use App\Exports\Mrp\MrpReportOrigSalesdataV3Export;
use App\ModelFilters\Mrp\MrpReportOrigSalesdataV3Filter;
use App\Models\Mrp\MrpReportOrigSalesdataV3;
use App\Models\UserImportExportRecord;
use App\Tools\Client\YksFileSystem;
use Carbon\Carbon;
use League\Csv\Writer;
use Maatwebsite\Excel\Facades\Excel;

//销量源数据
class MrpReportOrigSalesdataV3Service
{
    protected $downloadLimitRows = 100000;

    /**
     * 列表
     * @return mixed
     */
    public function list()
    {
        return $this->builder()->paginate(request()->input('perPage'));
    }

    /**
     * 列表
     * @return mixed
     * @throws InvalidRequestException
     */
    public function export()
    {
         ini_set('memory_limit', '4096M');
        $builder = $this->builder();
        if ($builder->count() > $this->downloadLimitRows) {
            throw new InvalidRequestException("导出记录数超{$this->downloadLimitRows}条请筛选条件");
        }
        $fileName = date('YmdHis').'_'."销量源数据.csv";
        Excel::store(new MrpReportOrigSalesdataV3Export($builder), $fileName, 'export', \Maatwebsite\Excel\Excel::CSV);
        return YksFileSystem::upload($fileName);
    }


    public function exportAsync($requestParam){
        $importExportRecord = $requestParam['import_export_record'];
        //拿请求中的数据 此处容易犯错
        $requestData = $requestParam['data'] ?? [];
        $filename = "销量源数据.csv";
        $builder = $this->builder($requestData);
//        Excel::store(new MrpReportOrigSalesdataV3Export($builder), $fileName, 'export', \Maatwebsite\Excel\Excel::CSV);
        $filePath = file_save_path($filename);
        $writer = Writer::createFromPath($filePath, 'w+');
        $header = [
            'id	',
            '订单号',
            'SKU',
            '数量',
            '平台',
            '成本价',
            '未做任何修正的日均',
            '近14天修正后日均',
            '出单账号',
            '销售团队',
            '主管账号',
            '经理账号',
            '仓库ID',
            '付款时间',
            '计算批次',
            '统计时间',
        ];
        $writer->insertOne($header);
        $builder->chunkById(3000, function ($list) use (&$writer) {
            $formatData = [];
            foreach ($list as $record) {
                $formatData[] = [
                    $record->id,
                    $record->package_code,
                    $record->sku,
                    $record->item_count,
                    $record->platform,
                    $record->price,
                    $record->avg_day_sales,
                    $record->nearly14days_qty,
                    $record->sales_account,
                    $record->business_type,
                    $record->zg_account,
                    $record->jl_account,
                    $record->warehouseid,
                    $record->payment_date,
                    $record->compute_batch,
                    $record->updated_at,
                ];
            }
            $writer->insertAll($formatData);
        });
        $importExportRecord->update(
            [
                'status'            => UserImportExportRecord::STATUS_SUCCESS,
                'file_download_url' => YksFileSystem::upload($filename),
                'completed_at'      => Carbon::now(),
                'result'            => '处理成功'
            ]
        );
    }

    /**
     * Desc:
     * @param $requestArr
     * @return mixed
     */
    private function builder($requestArr = [])
    {
        if (!$requestArr) {
            $requestArr = request()->input('data', []);
        }
        return MrpReportOrigSalesdataV3::query()
            ->select(
                'id',
                'package_code',
                'sku',
                'item_count',
                'platform',
                'price',
                'avg_day_sales',
                'nearly14days_qty',
                'sales_account',
                'zg_account',
                'jl_account',
                'business_type',
                'warehouseid',
                'payment_date',
                'compute_batch',
                'updated_at'
            )
            ->filter($requestArr, MrpReportOrigSalesdataV3Filter::class)
            ->orderByDesc('id');
    }
}
