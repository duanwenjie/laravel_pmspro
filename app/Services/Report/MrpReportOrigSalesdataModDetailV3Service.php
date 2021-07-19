<?php


namespace App\Services\Report;

use App\Exceptions\InvalidRequestException;
use App\Exports\Mrp\MrpReportOrigSalesdataModDetailV3Export;
use App\ModelFilters\Mrp\MrpReportOrigSalesdataModDetailV3Filter;
use App\Models\Mrp\MrpReportOrigSalesdataModDetailV3;
use App\Models\UserImportExportRecord;
use App\Tools\Client\YksFileSystem;
use Carbon\Carbon;
use League\Csv\Writer;
use Maatwebsite\Excel\Facades\Excel;

//MRP(国内)-》MRP V3-》修正前销售明细统计表
class MrpReportOrigSalesdataModDetailV3Service
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
    public function export($requestParam)
    {
         ini_set('memory_limit', '4096M');
        $importExportRecord = $requestParam['import_export_record'];
        $requestData = $requestParam['data'] ?? [];
        $builder = $this->builder($requestData);
        $fileName = date('YmdHis').'_'."修正前销售明细统计表.csv";
        /*if ($builder->count() > $this->downloadLimitRows) {
            throw new InvalidRequestException("导出记录数超{$this->downloadLimitRows}条请筛选条件");
        }
        $fileName = date('YmdHis').'_'."修正前销售明细统计表.csv";
        Excel::store(
            new MrpReportOrigSalesdataModDetailV3Export($builder),
            $fileName,
            'export',
            \Maatwebsite\Excel\Excel::CSV
        );
        return YksFileSystem::upload($fileName);*/
        $filePath = file_save_path($fileName);
        $writer = Writer::createFromPath($filePath, 'w+');
        $header = [
            'id',
            'SKU',
            date('Y-m-d', strtotime('-0 day')),
            date('Y-m-d', strtotime('-1 day')),
            date('Y-m-d', strtotime('-2 day')),
            date('Y-m-d', strtotime('-3 day')),
            date('Y-m-d', strtotime('-4 day')),
            date('Y-m-d', strtotime('-5 day')),
            date('Y-m-d', strtotime('-6 day')),
            date('Y-m-d', strtotime('-7 day')),
            date('Y-m-d', strtotime('-8 day')),
            date('Y-m-d', strtotime('-9 day')),
            date('Y-m-d', strtotime('-10 day')),
            date('Y-m-d', strtotime('-11 day')),
            date('Y-m-d', strtotime('-12 day')),
            date('Y-m-d', strtotime('-13 day')),
            '计算批次',
            '统计时间'
        ];
        $writer->insertOne($header);
        $builder->chunkById(3000, function ($list) use (&$writer) {
            $formatData = [];
            foreach ($list as $report) {
                $formatData[] = [
                    $report->id,
                    $report->sku,
                    $report->old_day_sales_1?:'0',
                    $report->old_day_sales_2?:'0',
                    $report->old_day_sales_3?:'0',
                    $report->old_day_sales_4?:'0',
                    $report->old_day_sales_5?:'0',
                    $report->old_day_sales_6?:'0',
                    $report->old_day_sales_7?:'0',
                    $report->old_day_sales_8?:'0',
                    $report->old_day_sales_9?:'0',
                    $report->old_day_sales_10?:'0',
                    $report->old_day_sales_11?:'0',
                    $report->old_day_sales_12?:'0',
                    $report->old_day_sales_13?:'0',
                    $report->old_day_sales_14?:'0',
                    $report->compute_batch,
                    $report->updated_at,
                ];
            }
            $writer->insertAll($formatData);
        });
        $importExportRecord->update(
            [
                'status'            => UserImportExportRecord::STATUS_SUCCESS,
                'file_download_url' => YksFileSystem::upload($fileName),
                'completed_at'      => Carbon::now(),
                'result'            => '处理成功'
            ]
        );

    }

    /**
     * 获取前端查询条件
     * @return mixed
     */
    private function builder($requestData = [])
    {
        if (!$requestData) {
            $requestData = request()->input('data', []);
        }
        return MrpReportOrigSalesdataModDetailV3::query()
            ->select(
                'id',
                'sku',
                'old_day_sales_1',
                'old_day_sales_2',
                'old_day_sales_3',
                'old_day_sales_4',
                'old_day_sales_5',
                'old_day_sales_6',
                'old_day_sales_7',
                'old_day_sales_8',
                'old_day_sales_9',
                'old_day_sales_10',
                'old_day_sales_11',
                'old_day_sales_12',
                'old_day_sales_13',
                'old_day_sales_14',
                'compute_batch',
                'updated_at'
            )
            ->filter($requestData, MrpReportOrigSalesdataModDetailV3Filter::class)
            ->orderByDesc('id');
    }
}
