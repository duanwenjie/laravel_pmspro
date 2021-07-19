<?php


namespace App\Services\Report;

use App\Exceptions\InvalidRequestException;
use App\Exports\Mrp\MrpReportOrdersModBefV3Export;
use App\ModelFilters\Mrp\MrpReportOrdersModBefV3Filter;
use App\Models\Mrp\MrpReportOrdersModBefV3;
use App\Tools\Client\YksFileSystem;
use Maatwebsite\Excel\Facades\Excel;

class MrpReportOrdersModBefV3Service
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
        $fileName = date('YmdHis').'_'."SKU日销量统计（修正前）.csv";
        Excel::store(new MrpReportOrdersModBefV3Export($builder), $fileName, 'export', \Maatwebsite\Excel\Excel::CSV);
        return YksFileSystem::upload($fileName);
    }

    /**
     * 获取前端查询条件
     * @return mixed
     */
    private function builder()
    {
        $request = request();
        return MrpReportOrdersModBefV3::query()->filter(
            $request->input('data', []),
            MrpReportOrdersModBefV3Filter::class
        )->orderByDesc('id');
    }
}
