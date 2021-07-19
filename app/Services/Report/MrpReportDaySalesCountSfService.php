<?php


namespace App\Services\Report;

use App\Exceptions\InvalidRequestException;
use App\Exports\Mrp\MrpReportDaySalesCountSfExport;
use App\ModelFilters\Mrp\MrpReportDaySalesCountSfFilter;
use App\Models\Mrp\MrpReportDaySalesCountSf;
use App\Models\UserImportExportRecord;
use App\Tools\Client\YksFileSystem;
use Carbon\Carbon;
use League\Csv\Writer;
use Maatwebsite\Excel\Facades\Excel;

class MrpReportDaySalesCountSfService
{
    protected $downloadLimitRows = 100000;

    /**
     * 列表
     * @return mixed
     */
    public function list()
    {
        $request = request();
        $res = $this->builder()
            ->paginate($request->input('perPage'));
        return $res;
    }

    /**
     * 导出
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
        $fileName = date('YmdHis').'_'."平台SKU日销量统计报表.csv";
        Excel::store(new MrpReportDaySalesCountSfExport($builder), $fileName, 'export', \Maatwebsite\Excel\Excel::CSV);
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
        $fileName = date('YmdHis').'_'."平台SKU日销量统计报表.csv";
        $builder = $this->builder($requestData);
        $filePath = file_save_path($fileName);
        $writer = Writer::createFromPath($filePath, 'w+');
        $header = [
            'id',
            'SKU',
        ];
        for ($i = 0; $i <= 29; $i++) {
            $d = $i;
            $beginDate = date("Y-m-d", strtotime("-$d day"));
            array_push($header, $beginDate);
        }
        array_push($header, '统计批次');
        array_push($header, '同步时间');
        $writer->insertOne($header);
        $builder->chunkById(3000, function ($list) use (&$writer) {
            $formatData = [];
            foreach ($list as $report) {
                $formatData[] = [
                    $report->id,//id
                    $report->sku,//SKU
                    $report->old_day_sales_1?:'0',//历史1天销量
                    $report->old_day_sales_2?:'0',//历史2天销量
                    $report->old_day_sales_3?:'0',//历史3天销量
                    $report->old_day_sales_4?:'0',//历史4天销量
                    $report->old_day_sales_5?:'0',//历史5天销量
                    $report->old_day_sales_6?:'0',//历史6天销量
                    $report->old_day_sales_7?:'0',//历史7天销量
                    $report->old_day_sales_8?:'0',//历史8天销量
                    $report->old_day_sales_9?:'0',//历史9天销量
                    $report->old_day_sales_10?:'0',//历史10天销量
                    $report->old_day_sales_11?:'0',//历史11天销量
                    $report->old_day_sales_12?:'0',//历史12天销量
                    $report->old_day_sales_13?:'0',//历史13天销量
                    $report->old_day_sales_14?:'0',//历史14天销量
                    $report->old_day_sales_15?:'0',//历史15天销量
                    $report->old_day_sales_16?:'0',//历史16天销量
                    $report->old_day_sales_17?:'0',//历史17天销量
                    $report->old_day_sales_18?:'0',//历史18天销量
                    $report->old_day_sales_19?:'0',//历史19天销量
                    $report->old_day_sales_20?:'0',//历史20天销量
                    $report->old_day_sales_21?:'0',//历史21天销量
                    $report->old_day_sales_22?:'0',//历史22天销量
                    $report->old_day_sales_23?:'0',//历史23天销量
                    $report->old_day_sales_24?:'0',//历史24天销量
                    $report->old_day_sales_25?:'0',//历史25天销量
                    $report->old_day_sales_26?:'0',//历史26天销量
                    $report->old_day_sales_27?:'0',//历史27天销量
                    $report->old_day_sales_28?:'0',//历史28天销量
                    $report->old_day_sales_29?:'0',//历史29天销量
                    $report->old_day_sales_30?:'0',//历史30天销量
                    $report->compute_batch,//统计批次
                    $report->updated_at,//同步时间
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
    private function builder($requestArr =[])
    {
        if (!$requestArr) {
            $requestArr = request()->input('data', []);
        }
        $columns = [
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
            'old_day_sales_10',
            'old_day_sales_11',
            'old_day_sales_12',
            'old_day_sales_13',
            'old_day_sales_14',
            'old_day_sales_15',
            'old_day_sales_16',
            'old_day_sales_17',
            'old_day_sales_18',
            'old_day_sales_19',
            'old_day_sales_20',
            'old_day_sales_21',
            'old_day_sales_22',
            'old_day_sales_23',
            'old_day_sales_24',
            'old_day_sales_25',
            'old_day_sales_26',
            'old_day_sales_27',
            'old_day_sales_28',
            'old_day_sales_29',
            'old_day_sales_30',
            'compute_batch',
            'updated_at'
        ];
        return MrpReportDaySalesCountSf::query()->select($columns)->filter(
            $requestArr,
            MrpReportDaySalesCountSfFilter::class
        )->orderByDesc('id');
    }
}
