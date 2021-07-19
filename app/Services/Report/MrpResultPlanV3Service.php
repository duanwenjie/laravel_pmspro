<?php


namespace App\Services\Report;

use App\Exceptions\InvalidRequestException;
use App\Exports\Mrp\MrpResultPlanV3Export;
use App\Http\ConfigBase\ConfigBase;
use App\ModelFilters\Mrp\MrpResultPlanV3Filter;
use App\Models\Mrp\MrpResultPlanV3;
use App\Tools\Client\YksFileSystem;
use App\Tools\Formater;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Database\Query\Builder;
use Maatwebsite\Excel\Facades\Excel;

/*MRP(国内)-》MRP V3-》计算SKU自动补货*/

class MrpResultPlanV3Service
{
    protected $downloadLimitRows = 100000;

    /**
     * 列表
     * @return LengthAwarePaginator
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
        $fileName = date('YmdHis').'_'."计算SKU自动补货.csv";
        Excel::store(new MrpResultPlanV3Export($builder), $fileName, 'export', \Maatwebsite\Excel\Excel::CSV);
        return YksFileSystem::upload($fileName);
    }

    /**
     * 获取前端查询条件
     * @return Builder
     */
    private function builder()
    {
        return MrpResultPlanV3::query()
            ->with([
                'skuInfo' => function ($query) {
                    $query->select(['sku', 'cn_name']);
                },
                'skuCore' => function ($query) {
                    $query->select(['sku', 'sku_mark']);
                }
            ])
            ->select(
                "id",
                "sku",
                "stock_way",
                "sales_status",
                "warehouseid",
                "stock_cycle",
                "stock_advance_cycle",
                "fixed_stock_num",
                "order_day_times_14",
                "day_sales_14",
                "sdv_day_sales",
                "nearly14days_qty",
                "sales_trend",
                "stocking_coefficient",
                "pr_count",
                "purchase_on_way_num",
                "actual_stock_num_6",
                "available_stock_num",
                "newwms_use_num",
                "actual_stock_num",
                "occupy_stock_num",
                "total_stock_num",
                "leave_num",
                "price",
                "order_point",
                "replenishment_num",
                "remark",
                "request_date",
                "compute_batch",
                "updated_at",
                "confirm_status",
                "planner_nick"
            )
            ->filter(request()->input('data', []), MrpResultPlanV3Filter::class)
            ->orderByDesc('id');
    }


    /**
     * 字典
     * @return array
     */
    public function getMrpResultPlanV3Dict()
    {
        return [
            'stockWay'      => Formater::formatDict(ConfigBase::getStockWayMap()),
            'salesStatus'   => Formater::formatDict(ConfigBase::getSalesStatusMap()),
            'confirmStatus' => Formater::formatDict(ConfigBase::getConfirmStatusMap()),
        ];
    }
}
