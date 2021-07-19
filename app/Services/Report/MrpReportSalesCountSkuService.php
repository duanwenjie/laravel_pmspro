<?php

namespace App\Services\Report;

use App\Exceptions\InvalidRequestException;
use App\Exports\Mrp\MrpReportSalesCountSkuExport;
use App\ModelFilters\Mrp\MrpReportSalesCountSkuFilter;
use App\Models\Mrp\MrpReportSalesCountSku;
use App\Tools\Client\YksFileSystem;
use Maatwebsite\Excel\Facades\Excel;

//MRP(国内)-》MRP V3-》销量-SKU统计
class MrpReportSalesCountSkuService
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
        $fileName = date('YmdHis').'_'."销量-SKU统计.csv";
        Excel::store(new MrpReportSalesCountSkuExport($builder), $fileName, 'export', \Maatwebsite\Excel\Excel::CSV);
        return YksFileSystem::upload($fileName);
    }

    /**
     * 获取前端查询条件
     * @return mixed
     */
    private function builder()
    {
        return MrpReportSalesCountSku::query()->select(
            "id",
            "sku_count",
            "days_sku_count7",
            "days_sku_count14",
            "days_sku_count30",
            "days_sku_count60",
            "days_sku_count90",
            "days_sku_count180",
            "updated_at"
        )
            ->filter(request()->input('data', []), MrpReportSalesCountSkuFilter::class)
            ->orderByRaw('updated_at DESC, id DESC');
    }
}
