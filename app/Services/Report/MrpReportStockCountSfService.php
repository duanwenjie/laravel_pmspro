<?php


namespace App\Services\Report;

use App\Exceptions\InvalidRequestException;
use App\Exports\Mrp\MrpReportStockCountSfExport;
use App\ModelFilters\Mrp\MrpReportStockCountSfFilter;
use App\Models\Mrp\MrpReportStockCountSf;
use App\Models\UserImportExportRecord;
use App\Tools\Client\YksFileSystem;
use Carbon\Carbon;
use League\Csv\Writer;
use Maatwebsite\Excel\Facades\Excel;

class MrpReportStockCountSfService
{
    protected $downloadLimitRows = 100000;

    /**
     * 列表
     * @return mixed
     */
    public function list()
    {
        $request = request();
        return $this->builder()
            ->paginate($request->input('perPage'));
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
        $fileName = date('YmdHis').'_'."库存统计(HS).csv";
        Excel::store(new MrpReportStockCountSfExport($builder), $fileName, 'export', \Maatwebsite\Excel\Excel::CSV);
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
        $fileName = date('YmdHis').'_'."库存统计(HS).csv";
//        Excel::store(new MrpReportStockCountSfExport($builder), $fileName, 'export', \Maatwebsite\Excel\Excel::CSV);
        $builder = $this->builder($requestData);
        $filePath = file_save_path($fileName);
        $writer = Writer::createFromPath($filePath, 'w+');
        $header = [
            'id',
            'SKU',
            '备货方式',
            '销售状态',
            '出单次数',
            'PR数',
            '未生成PO',
            '已建单且未打印',
            '采购在途',
            '可用库存',
            '实际库存数量',
            'WMS占用库存',
            '总未发数量',
            '总可用库存',
            '入库标识',
            '计算批次',
            '统计时间',
        ];
        $writer->insertOne($header);
        $builder->chunkById(3000, function ($list) use (&$writer) {
            $formatData = [];
            foreach ($list as $report) {
                $formatData[] = [
                    $report->id,//id
                    $report->sku,//SKU
                    $report->stock_way_name,//备货方式描述
                    $report->sales_status_name,//销售状态描述
                    $report->order_times,//出单次数
                    $report->pr_count,//PR数
                    $report->no_order_pr_num,//未生成PO
                    $report->no_print_num,//已建单且未打印
                    $report->purchase_on_way_num,//采购在途
                    $report->available_stock_num,//可用库存
                    $report->actual_stock_num,//实际库存数量
                    $report->newwms_use_num,//WMS占用库存
                    $report->occupy_stock_num,//总未发数量
                    $report->total_stock_num,//总可用库存
                    $report->sku_ware_record,//入库标识
                    $report->compute_batch,//计算批次
                    $report->updated_at,//统计时间
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
            'stock_way',
            'sales_status',
            'order_times',
            'pr_count',
            'no_order_pr_num',
            'no_print_num',
            'purchase_on_way_num',
            'available_stock_num',
            'actual_stock_num',
            'newwms_use_num',
            'occupy_stock_num',
            'total_stock_num',
            'sku_ware_record',
            'compute_batch',
            'updated_at'
        ];
        return MrpReportStockCountSf::query()->select($columns)->filter($requestArr,
            MrpReportStockCountSfFilter::class
        )->orderByDesc('id');
    }
}
