<?php


namespace App\Services\Report;

use App\Exceptions\InvalidRequestException;
use App\Exports\Mrp\MrpResultPlanSfExport;
use App\ModelFilters\Mrp\MrpResultPlanSfFilter;
use App\Models\Mrp\MrpResultPlanSf;
use App\Tools\Client\YksFileSystem;
use App\Tools\Formater;
use Maatwebsite\Excel\Facades\Excel;

class MrpResultPlanSfService
{
    protected $downloadLimitRows = 100000;

    /**
     * 列表
     * @return mixed
     */
    public function list()
    {
        $request = request();
        $res = $this->builder()
            ->paginate($request->input('perPage'));
        return $res;
    }

    /**
     * 导出
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
        $fileName = date('YmdHis').'_'."自动补货建议(HS).csv";
        Excel::store(new MrpResultPlanSfExport($builder), $fileName, 'export', \Maatwebsite\Excel\Excel::CSV);
        return YksFileSystem::upload($fileName);
    }

    /**
     * 获取前端查询条件
     * @return mixed
     */
    private function builder()
    {
        $request = request();
        $columns = [
            'sku',
            'stock_way',
            'sales_status',
            'warehouseid',
            'fixed_stock_num',
            'buffer_stock_cycle',
            'supply_cycle',
            'order_times',
            'day_sales',
            'nearly1days_qty',
            'nearly2days_qty',
            'nearly3days_qty',
            'sales_trend',
            'pr_count',
            'purchase_on_way_num',
            'available_stock_num',
            'actual_stock_num',
            'newwms_use_num',
            'occupy_stock_num',
            'total_stock_num',
            'order_point',
            'replenishment_num',
            'request_date',
            'compute_batch',
            'confirm_status',
            'sku_mark',
            'price',
            'planner_nick',
            'updated_at'
        ];
        return MrpResultPlanSf::query()->select($columns)->with('skuInfo')->filter(
            $request->input('data', []),
            MrpResultPlanSfFilter::class
        )->orderByDesc('id');
    }

    /**
     * 字典
     * @return array
     * @author dwj
     */
    public function getMsrpResultPlanSf()
    {
        return [
            'stockWay'      => Formater::formatDict(MrpResultPlanSf::$stockWay),
            'salesStatus'   => Formater::formatDict(MrpResultPlanSf::$salesStatus),
            'confirmStatus' => Formater::formatDict(MrpResultPlanSf::$confirmStatus),
            'salesTrend'    => Formater::formatDict(MrpResultPlanSf::$salesTrend),
        ];
    }
}
