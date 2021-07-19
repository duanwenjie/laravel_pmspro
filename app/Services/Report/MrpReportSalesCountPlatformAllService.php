<?php


namespace App\Services\Report;

use App\Exceptions\InvalidRequestException;
use App\Exports\Mrp\MrpReportSalesCountPlatformAllExport;
use App\ModelFilters\Mrp\MrpReportSalesCountPlatformAllFilter;
use App\Models\Mrp\MrpReportSalesCountPlatformAll;
use App\Models\UserImportExportRecord;
use App\Tools\Client\YksFileSystem;
use Carbon\Carbon;
use League\Csv\Writer;
use Maatwebsite\Excel\Facades\Excel;

//MRP(国内)-》平台+SKU销量统计(不剔除)
class MrpReportSalesCountPlatformAllService
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
        /*
        $builder = $this->builder();
        if ($builder->count() > $this->downloadLimitRows) {
            throw new InvalidRequestException("导出记录数超{$this->downloadLimitRows}条请筛选条件");
        }
        $fileName = date('YmdHis').'_'."平台+SKU销量统计(不剔除).csv";
        Excel::store(
            new MrpReportSalesCountPlatformAllExport($builder),
            $fileName,
            'export',
            \Maatwebsite\Excel\Excel::CSV
        );
        return YksFileSystem::upload($fileName);*/
        $importExportRecord = $requestParam['import_export_record'];
        $requestData = $requestParam['data'] ?? [];
        $fileName = date('YmdHis').'_'."平台+SKU销量统计(不剔除).csv";
        $builder = $this->builder($requestData);
        $filePath = file_save_path($fileName);
        $writer = Writer::createFromPath($filePath, 'w+');
        $header = [
            "序号",
            "SKU",
            "平台",
            "7天销量",
            "14天销量",
            "28天销量",
            "累计待发销量",
            "统计时间"
        ];
        $writer->insertOne($header);
        $builder->chunkById(3000, function ($list) use (&$writer) {
            $formatData = [];
            foreach ($list as $report) {
                $formatData[] = [
                    $report->id,
                    $report->sku,
                    $report->platform_code,
                    $report->day_sales_7,
                    $report->day_sales_14,
                    $report->day_sales_28,
                    $report->total_sales,
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
        return MrpReportSalesCountPlatformAll::query()
            ->select(
                "id",
                "sku",
                "platform_code",
                "day_sales_7",
                "day_sales_14",
                "day_sales_28",
                "total_sales",
                "updated_at"
            )
            ->filter($requestData, MrpReportSalesCountPlatformAllFilter::class)
            ->orderByDesc('id');
    }
}
