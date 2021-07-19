<?php


namespace App\Services\Report;

use App\Exceptions\InvalidRequestException;
use App\Exports\Mrp\MrpReportOrdersModBefDetailV3Export;
use App\ModelFilters\Mrp\MrpReportOrdersModBefDetailV3Filter;
use App\Models\Mrp\MrpReportOrdersModBefDetailV3;
use App\Tools\Client\YksFileSystem;
use Maatwebsite\Excel\Facades\Excel;

//MRP(国内)-》MRP V3-》修正后销售明细统计表
class MrpReportOrdersModBefDetailV3Service
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
        $fileName = date('YmdHis').'_'."修正后销售明细统计表.csv";
        Excel::store(
            new MrpReportOrdersModBefDetailV3Export($builder),
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
        return MrpReportOrdersModBefDetailV3::query()
            ->select(
                'id',
                'sku',
                'old_day_sales_1',
                'old_day_sales_2',
                'old_day_sales_3',
                'old_day_sales_4',
                'old_day_sales_5',
                'old_day_sales_6',
                'old_day_sales_7',
                'old_day_sales_8',
                'old_day_sales_9',
                'old_day_sales_10',
                'old_day_sales_11',
                'old_day_sales_12',
                'old_day_sales_13',
                'old_day_sales_14',
                'compute_batch',
                'updated_at'
            )
            ->filter(request()->input('data', []), MrpReportOrdersModBefDetailV3Filter::class)
            ->orderByDesc('id');
    }
}
