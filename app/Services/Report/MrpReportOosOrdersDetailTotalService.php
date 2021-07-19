<?php


namespace App\Services\Report;

use App\Exceptions\InvalidRequestException;
use App\Exports\Mrp\MrpReportOosOrdersDetailTotalExport;
use App\ModelFilters\Mrp\MrpReportOosOrdersDetailTotalFilter;
use App\Models\Mrp\MrpReportOosOrdersDetailTotal;
use App\Tools\Client\YksFileSystem;
use Maatwebsite\Excel\Facades\Excel;

//MRP(国内)-》总缺货订单明细
class MrpReportOosOrdersDetailTotalService
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
        $fileName = date('YmdHis').'_'."总缺货订单明细.csv";
        Excel::store(
            new MrpReportOosOrdersDetailTotalExport($builder),
            $fileName,
            'export',
            \Maatwebsite\Excel\Excel::CSV
        );
        return YksFileSystem::upload($fileName);
    }

    /**
     * 获取前端查询条件
     * @return mixed
     */
    private function builder()
    {
        return MrpReportOosOrdersDetailTotal::query()
            ->select(
                "package_code",
                "sku",
                "total_amount",
                "order_status",
                "item_count",
                "platform",
                "sales_account",
                "dw_date",
                "payment_date"
            )
            ->filter(request()->input('data', []), MrpReportOosOrdersDetailTotalFilter::class)
            ->orderByRaw('dw_date DESC, payment_date DESC');
    }
}
