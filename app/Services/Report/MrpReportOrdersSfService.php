<?php


namespace App\Services\Report;

use App\Exceptions\InvalidRequestException;
use App\Exports\Mrp\MrpReportOrdersSfExport;
use App\ModelFilters\Mrp\MrpReportOrdersSfFilter;
use App\Models\Mrp\MrpReportOrdersSf;
use App\Models\UserImportExportRecord;
use App\Tools\Client\YksFileSystem;
use Carbon\Carbon;
use League\Csv\Writer;
use Maatwebsite\Excel\Facades\Excel;

class MrpReportOrdersSfService
{
    protected $downloadLimitRows = 100000;

    /**
     * 列表
     * @return mixed
     */
    public function list()
    {
        $request = request();
        $res = $this->builder()->orderByDesc('id')
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
        $fileName = date('YmdHis').'_'."销量统计(HS).csv";
        Excel::store(new MrpReportOrdersSfExport($builder), $fileName, 'export', \Maatwebsite\Excel\Excel::CSV);
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
        $fileName = date('YmdHis').'_'."销量统计(HS).csv";
//        Excel::store(new MrpReportOrdersSfExport($builder), $fileName, 'export', \Maatwebsite\Excel\Excel::CSV);
        $builder = $this->builder($requestData);
        $filePath = file_save_path($fileName);
        $writer = Writer::createFromPath($filePath, 'w+');
        $header = [
            'SKU',
            '备货方式',
            '销售状态',
            '出单次数',
            '倒推第1天销量',
            '倒推第2天销量',
            '倒推第3天销量',
            '销量趋势',
            '近7天销量',
            '近14天销量',
            '近30天销量',
            '近55天销量',
            '日均销量',
            '订购点',
            '计算批次',
            '统计时间',
        ];
        $writer->insertOne($header);
        $builder->chunkById(3000, function ($list) use (&$writer) {
            $formatData = [];
            foreach ($list as $report) {
                $formatData[] = [
                    $report->sku,
                    $report->stock_way_name,
                    $report->sales_status_name,
                    $report->order_times,
                    $report->nearly1days_qty,
                    $report->nearly2days_qty,
                    $report->nearly3days_qty,
                    $report->sales_trend_des,
                    $report->nearly7days_qty,
                    $report->nearly14days_qty,
                    $report->nearly30days_qty,
                    $report->nearly55days_qty,
                    $report->day_sales,
                    $report->order_point,
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
     * Desc: 获取前端查询条件
     * @param $requestArr
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
            'stock_way',
            'sales_status',
            'order_times',
            'nearly1days_qty',
            'nearly2days_qty',
            'nearly3days_qty',
            'sales_trend',
            'nearly7days_qty',
            'nearly14days_qty',
            'nearly30days_qty',
            'nearly55days_qty',
            'day_sales',
            'order_point',
            'compute_batch',
            'updated_at'
        ];
        return MrpReportOrdersSf::query()->select($columns)->filter($requestArr,
            MrpReportOrdersSfFilter::class
        );
    }
}
