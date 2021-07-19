<?php


namespace App\Services\Report;

use App\Exceptions\InvalidRequestException;
use App\Exports\Mrp\MrpReportOrigSalesdataSfExport;
use App\ModelFilters\Mrp\MrpReportOrigSalesdataSfFilter;
use App\Models\Mrp\MrpReportOrigSalesdataSf;
use App\Tools\Client\YksFileSystem;
use Maatwebsite\Excel\Facades\Excel;

class MrpReportOrigSalesdataSfService
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
        $fileName = date('YmdHis').'_'."销量源数据(HS).csv";
        Excel::store(new MrpReportOrigSalesdataSfExport($builder), $fileName, 'export', \Maatwebsite\Excel\Excel::CSV);
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
            'id',
            'package_code',
            'sku',
            'item_count',
            'platform',
            'warehouse',
            'warehouseid',
            'orders_export_time',
            'payment_date',
            'order_create_time',
            'out_time',
            'orders_out_time',
            'compute_batch',
            'updated_at'
        ];
        return MrpReportOrigSalesdataSf::query()->select($columns)->filter(
            $request->input('data', []),
            MrpReportOrigSalesdataSfFilter::class
        )->orderByDesc('id');
    }
}
