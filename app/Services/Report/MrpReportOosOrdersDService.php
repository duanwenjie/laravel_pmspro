<?php


namespace App\Services\Report;

use App\Exceptions\InvalidRequestException;
use App\Exports\Mrp\MrpReportOosOrdersDExport;
use App\ModelFilters\Mrp\MrpReportOosOrdersDFilter;
use App\Models\Mrp\MrpReportOosOrdersD;
use App\Tools\Client\YksFileSystem;
use Maatwebsite\Excel\Facades\Excel;

/*MRP(国内)-》日缺货率统计报表*/

class MrpReportOosOrdersDService
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
        $fileName = date('YmdHis').'_'."日缺货率统计报表.csv";
        Excel::store(new MrpReportOosOrdersDExport($builder), $fileName, 'export', \Maatwebsite\Excel\Excel::CSV);
        return YksFileSystem::upload($fileName);
    }

    /**
     * 获取前端查询条件
     * @return mixed
     */
    private function builder()
    {
        return MrpReportOosOrdersD::query()
            ->select(
                "cancel_orders_qty",
                "total_orders_qty",
                "cancel_orders_qty_rate",
                "cancel_orders_amount",
                "total_orders_amount",
                "cancel_orders_amount_rate",
                "orders_export_time",
                "updated_at"
            )
            ->filter(request()->input('data', []), MrpReportOosOrdersDFilter::class)
            ->orderByDesc('orders_export_time');
    }
}
