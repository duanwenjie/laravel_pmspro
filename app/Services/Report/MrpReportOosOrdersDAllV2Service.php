<?php


namespace App\Services\Report;

use App\Exceptions\InvalidRequestException;
use App\Exports\Mrp\MrpReportOosOrdersDAllV2Export;
use App\ModelFilters\Mrp\MrpReportOosOrdersDAllV2Filter;
use App\Models\Mrp\MrpReportOosOrdersDAllV2;
use App\Tools\Client\YksFileSystem;
use Maatwebsite\Excel\Facades\Excel;

//MRP(国内)-》撤单和缺货订单日统计
class MrpReportOosOrdersDAllV2Service
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
        $fileName = date('YmdHis').'_'."撤单和缺货订单日统计.csv";
        Excel::store(new MrpReportOosOrdersDAllV2Export($builder), $fileName, 'export', \Maatwebsite\Excel\Excel::CSV);
        return YksFileSystem::upload($fileName);
    }

    /**
     * 获取前端查询条件
     * @return mixed
     */
    private function builder()
    {
        return MrpReportOosOrdersDAllV2::query()
            ->select(
                "cancel_orders_qty",
                "qh_orders_qty",
                "cancel_orders_amount",
                "qh_orders_amount",
                "orders_export_time",
                "updated_at"
            )
            ->filter(request()->input('data', []), MrpReportOosOrdersDAllV2Filter::class)
            ->orderByRaw('updated_at DESC, orders_export_time ASC');
    }
}
