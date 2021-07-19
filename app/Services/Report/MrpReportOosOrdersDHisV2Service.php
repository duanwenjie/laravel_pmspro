<?php


namespace App\Services\Report;

use App\Exceptions\InvalidRequestException;
use App\Exports\Mrp\MrpReportOosOrdersDHisV2Export;
use App\ModelFilters\Mrp\MrpReportOosOrdersDHisV2Filter;
use App\Models\Mrp\MrpReportOosOrdersDHisV2;
use App\Tools\Client\YksFileSystem;
use Maatwebsite\Excel\Facades\Excel;

//MRP(国内)-》历史每日缺货占比统计表
class MrpReportOosOrdersDHisV2Service
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
        $fileName = date('YmdHis').'_'."历史每日缺货占比统计表.csv";
        Excel::store(new MrpReportOosOrdersDHisV2Export($builder), $fileName, 'export', \Maatwebsite\Excel\Excel::CSV);
        return YksFileSystem::upload($fileName);
    }

    /**
     * 获取前端查询条件
     * @return mixed
     */
    private function builder()
    {
        return MrpReportOosOrdersDHisV2::query()
            ->select(
                "platform",
                "cancel_orders_qty",
                "total_orders_qty",
                "cancel_orders_qty_rate",
                "cancel_orders_amount",
                "total_orders_amount",
                "cancel_orders_amount_rate",
                "payment_date",
                "updated_at"
            )
            ->filter(request()->input('data', []), MrpReportOosOrdersDHisV2Filter::class)
            ->orderByRaw('updated_at DESC, payment_date ASC');
    }
}
