<?php


namespace App\Services\Report;

use App\Exceptions\InvalidRequestException;
use App\Exports\Mrp\MrpReportDaySalesCountExport;
use App\ModelFilters\Mrp\MrpReportDaySalesCountFilter;
use App\Models\Mrp\MrpReportDaySalesCount;
use App\Models\UserImportExportRecord;
use App\Tools\Client\YksFileSystem;
use Carbon\Carbon;
use League\Csv\Writer;
use Maatwebsite\Excel\Facades\Excel;

/*MRP(国内)-》sku日均销量统计报表*/

class MrpReportDaySalesCountService
{
    protected $downloadLimitRows = 100000;

    /**
     * 列表
     * @return mixed
     */
    public function list()
    {
        return $this->builder()->orderByDesc('id')->paginate(request()->input('perPage'));
    }

    private function builder($requestArr =[])
    {
        if (!$requestArr) {
            $requestArr = request()->input('data', []);
        }
        return MrpReportDaySalesCount::query()->select(
            "id",
            "sku",
            "old_day_sales_30",
            "old_day_sales_29",
            "old_day_sales_28",
            "old_day_sales_27",
            "old_day_sales_26",
            "old_day_sales_25",
            "old_day_sales_24",
            "old_day_sales_23",
            "old_day_sales_22",
            "old_day_sales_21",
            "old_day_sales_20",
            "old_day_sales_19",
            "old_day_sales_18",
            "old_day_sales_17",
            "old_day_sales_16",
            "old_day_sales_15",
            "old_day_sales_14",
            "old_day_sales_13",
            "old_day_sales_12",
            "old_day_sales_11",
            "old_day_sales_10",
            "old_day_sales_9",
            "old_day_sales_8",
            "old_day_sales_7",
            "old_day_sales_6",
            "old_day_sales_5",
            "old_day_sales_4",
            "old_day_sales_3",
            "old_day_sales_2",
            "old_day_sales_1",
            "compute_batch",
            "updated_at"
        )
            ->filter($requestArr, MrpReportDaySalesCountFilter::class);

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
        $fileName = date('YmdHis').'_'."sku日均销量统计报表.csv";
        Excel::store(new MrpReportDaySalesCountExport($builder), $fileName, 'export', \Maatwebsite\Excel\Excel::CSV);
        return YksFileSystem::upload($fileName);
    }


    /**
     * Desc:
     * @param $requestParam
     * @throws InvalidRequestException
     * @throws \League\Csv\CannotInsertRecord
     */
    public function exportAsync($requestParam)
    {
        ini_set('memory_limit', '4096M');
        $importExportRecord = $requestParam['import_export_record'];
        //拿请求中的数据 此处容易犯错
        $requestData = $requestParam['data'] ?? [];
        $fileName = date('YmdHis').'_'."sku日均销量统计报表.csv";
        $builder = $this->builder($requestData);
        $filePath = file_save_path($fileName);
        $writer = Writer::createFromPath($filePath, 'w+');
        $header = [
            '序号',
            'SKU',
            date('Y-m-d', strtotime('-29 day')),
            date('Y-m-d', strtotime('-28 day')),
            date('Y-m-d', strtotime('-27 day')),
            date('Y-m-d', strtotime('-26 day')),
            date('Y-m-d', strtotime('-25 day')),
            date('Y-m-d', strtotime('-24 day')),
            date('Y-m-d', strtotime('-23 day')),
            date('Y-m-d', strtotime('-22 day')),
            date('Y-m-d', strtotime('-21 day')),
            date('Y-m-d', strtotime('-20 day')),
            date('Y-m-d', strtotime('-19 day')),
            date('Y-m-d', strtotime('-18 day')),
            date('Y-m-d', strtotime('-17 day')),
            date('Y-m-d', strtotime('-16 day')),
            date('Y-m-d', strtotime('-15 day')),
            date('Y-m-d', strtotime('-14 day')),
            date('Y-m-d', strtotime('-13 day')),
            date('Y-m-d', strtotime('-12 day')),
            date('Y-m-d', strtotime('-11 day')),
            date('Y-m-d', strtotime('-10 day')),
            date('Y-m-d', strtotime('-9 day')),
            date('Y-m-d', strtotime('-8 day')),
            date('Y-m-d', strtotime('-7 day')),
            date('Y-m-d', strtotime('-6 day')),
            date('Y-m-d', strtotime('-5 day')),
            date('Y-m-d', strtotime('-4 day')),
            date('Y-m-d', strtotime('-3 day')),
            date('Y-m-d', strtotime('-2 day')),
            date('Y-m-d', strtotime('-1 day')),
            date('Y-m-d', strtotime('-0 day')),
            '统计时间',
            '同步时间'
        ];
        $writer->insertOne($header);
        $builder->chunkById(3000, function ($list) use (&$writer) {
            $formatData = [];
            foreach ($list as $report) {
                $formatData[] = [
                    $report->id,
                    $report->sku,
                    $report->old_day_sales_30?:'0',
                    $report->old_day_sales_29?:'0',
                    $report->old_day_sales_28?:'0',
                    $report->old_day_sales_27?:'0',
                    $report->old_day_sales_26?:'0',
                    $report->old_day_sales_25?:'0',
                    $report->old_day_sales_24?:'0',
                    $report->old_day_sales_23?:'0',
                    $report->old_day_sales_22?:'0',
                    $report->old_day_sales_21?:'0',
                    $report->old_day_sales_20?:'0',
                    $report->old_day_sales_19?:'0',
                    $report->old_day_sales_18?:'0',
                    $report->old_day_sales_17?:'0',
                    $report->old_day_sales_16?:'0',
                    $report->old_day_sales_15?:'0',
                    $report->old_day_sales_14?:'0',
                    $report->old_day_sales_13?:'0',
                    $report->old_day_sales_12?:'0',
                    $report->old_day_sales_11?:'0',
                    $report->old_day_sales_10?:'0',
                    $report->old_day_sales_9?:'0',
                    $report->old_day_sales_8?:'0',
                    $report->old_day_sales_7?:'0',
                    $report->old_day_sales_6?:'0',
                    $report->old_day_sales_5?:'0',
                    $report->old_day_sales_4?:'0',
                    $report->old_day_sales_3?:'0',
                    $report->old_day_sales_2?:'0',
                    $report->old_day_sales_1?:'0',
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
}
