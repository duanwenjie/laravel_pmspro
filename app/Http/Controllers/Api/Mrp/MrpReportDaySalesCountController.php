<?php


namespace App\Http\Controllers\Api\Mrp;

use App\Exceptions\InvalidRequestException;
use App\Http\Controllers\Controller;
use App\Http\Resources\Api\Mrp\MrpReportDaySalesCountResource;
use App\Models\UserImportExportRecord;
use App\Services\ExportService;
use App\Services\Report\MrpReportDaySalesCountService;
use App\Services\Report\MrpReportStockCountSfService;
use Illuminate\Http\JsonResponse;

/*销量源数据*/

class MrpReportDaySalesCountController extends Controller
{

    /**
     * 列表
     * @param  MrpReportDaySalesCountService  $befDetailV3Service
     * @return JsonResponse
     */
    public function list(MrpReportDaySalesCountService $befDetailV3Service)
    {
        $list = $befDetailV3Service->list();
        return $this->successForResourcePage(MrpReportDaySalesCountResource::collection($list));
    }


    /**
     * 导出
     * @param  MrpReportDaySalesCountService  $befDetailV3Service
     * @return JsonResponse
     * @throws InvalidRequestException
     */
    public function export(MrpReportDaySalesCountService $befDetailV3Service)
    {
//        $url = $befDetailV3Service->export();
//        return $this->success('导出成功', ['url' => $url]);


        (new ExportService())->asyncExport(
            UserImportExportRecord::MODULE_GN_MRP,
            'sku日均销量统计报表.csv',
            [new MrpReportDaySalesCountService(), 'exportAsync']
        );
        return $this->success('导出已加入队列');

    }
}
