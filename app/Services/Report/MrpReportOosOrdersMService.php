<?php


namespace App\Services\Report;

use App\Exceptions\InvalidRequestException;
use App\Exports\Mrp\MrpReportOosOrdersMExport;
use App\ModelFilters\Mrp\MrpReportOosOrdersMFilter;
use App\Models\Mrp\MrpReportOosOrdersM;
use App\Tools\Client\YksFileSystem;
use Maatwebsite\Excel\Facades\Excel;

//MRP(国内)-》月缺货率统计报表
class MrpReportOosOrdersMService
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
        $fileName = date('YmdHis').'_'."月缺货率统计报表.csv";
        Excel::store(new MrpReportOosOrdersMExport($builder), $fileName, 'export', \Maatwebsite\Excel\Excel::CSV);
        return YksFileSystem::upload($fileName);
    }

    /**
     * 获取前端查询条件
     * @return mixed
     */
    private function builder()
    {
        return MrpReportOosOrdersM::query()
            ->select(
                "cancel_orders_qty",
                "total_orders_qty",
                "cancel_orders_qty_rate",
                "cancel_orders_amount",
                "total_orders_amount",
                "cancel_orders_amount_rate",
                "orders_export_month",
                "updated_at"
            )
            ->filter(request()->input('data', []), MrpReportOosOrdersMFilter::class)
            ->orderByDesc('orders_export_month');
    }
}
