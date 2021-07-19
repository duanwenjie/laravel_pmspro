<?php


namespace App\Services\Report;

use App\Exceptions\InvalidRequestException;
use App\Exports\Mrp\MrpReportOrigSalesdataModV3Export;
use App\ModelFilters\Mrp\MrpReportOrigSalesdataModV3Filter;
use App\Models\Mrp\MrpReportOrigSalesdataModV3;
use App\Models\UserImportExportRecord;
use App\Tools\Client\YksFileSystem;
use Carbon\Carbon;
use League\Csv\Writer;
use Maatwebsite\Excel\Facades\Excel;

//销量源数据（修正后）
class MrpReportOrigSalesdataModV3Service
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
        ini_set('memory_limit', '1024M');
        $builder = $this->builder();
        $fileName = date('YmdHis').'_'."销量源数据（修正后）.csv";
        Excel::store(
            new MrpReportOrigSalesdataModV3Export($builder),
            $fileName,
            'export',
            \Maatwebsite\Excel\Excel::CSV
        );
        return YksFileSystem::upload($fileName);
    }

    public function exportAsync($requestParam){
        $importExportRecord = $requestParam['import_export_record'];
        //拿请求中的数据 此处容易犯错
        $requestData = $requestParam['data'] ?? [];
        $filename = "销量源数据（修正后）.csv";
        $builder = $this->builder($requestData);
//        Excel::store(new MrpReportOrigSalesdataModV3Export($builder), $fileName, 'export', \Maatwebsite\Excel\Excel::CSV);
        $filePath = file_save_path($filename);
        $writer = Writer::createFromPath($filePath, 'w+');
        $header = [
            'id	',
            '订单号',
            'SKU',
            '数量',
            '平台',
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
                    $record->platform_code,
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
     * Desc:获取前端查询条件
     * @param  array  $requestArr
     * @return mixed
     */
    private function builder($requestArr =[])
    {

        if (!$requestArr) {
            $requestArr = request()->input('data', []);
        }
        return MrpReportOrigSalesdataModV3::query()
            ->select(
                'id',
                'package_code',
                'sku',
                'item_count',
                'platform_code',
                'warehouseid',
                'payment_date',
                'compute_batch',
                'updated_at'
            )
            ->filter($requestArr, MrpReportOrigSalesdataModV3Filter::class)
            ->orderByDesc('id');
    }
}
