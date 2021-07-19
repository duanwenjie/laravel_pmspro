<?php


namespace App\Services\Report;

use App\Exceptions\InvalidRequestException;
use App\Exports\Mrp\MrpReportStockCountV3Export;
use App\ModelFilters\Mrp\MrpReportStockCountV3Filter;
use App\Models\Mrp\MrpReportStockCountV3;
use App\Tools\Client\YksFileSystem;
use Maatwebsite\Excel\Facades\Excel;

class MrpReportStockCountV3Service
{
    protected $downloadLimitRows = 100000;

    /**
     * 根据查询条件导出数据
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
        $fileName = date('YmdHis').'_'."SKU库存统计.csv";
        Excel::store(new MrpReportStockCountV3Export($builder), $fileName, 'export', \Maatwebsite\Excel\Excel::CSV);
        return YksFileSystem::upload($fileName);
    }

    /**
     * 获取前端查询条件
     * @return mixed
     */
    private function builder()
    {
        $request = request();
        return MrpReportStockCountV3::query()->filter(
            $request->input('data', []),
            MrpReportStockCountV3Filter::class
        )->orderByDesc('id');
    }
}
