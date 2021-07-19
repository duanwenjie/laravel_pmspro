<?php

namespace App\Http\Controllers\Api\Mrp;

use App\Exceptions\InvalidRequestException;
use App\Http\Controllers\Controller;
use App\Http\Resources\Api\Mrp\MrpReportDaySalesCountSfResource;
use App\Models\UserImportExportRecord;
use App\Services\ExportService;
use App\Services\Report\MrpReportDaySalesCountSfService;
use App\Services\Report\MrpReportStockCountSfService;
use Illuminate\Http\JsonResponse;

/*MRP(国内)-》MRP SF-》shopify平台sku日销量统计报表*/

class MrpReportDaySalesCountSfController extends Controller
{
    /**
     * 列表
     * @param  MrpReportDaySalesCountSfService  $countSfService
     * @return JsonResponse
     */
    public function list(MrpReportDaySalesCountSfService $countSfService)
    {
        $resource = $countSfService->list();
        return $this->successForResourcePage(MrpReportDaySalesCountSfResource::collection($resource));
    }

    /**
     * 导出
     * @param  MrpReportDaySalesCountSfService  $countSfService
     * @return JsonResponse
     * @throws InvalidRequestException
     */
    public function export(MrpReportDaySalesCountSfService $countSfService)
    {
        (new ExportService())->asyncExport(
            UserImportExportRecord::MODULE_GN_MRP,
            '平台SKU日销量统计报表(HS).csv',
            [new MrpReportDaySalesCountSfService(), 'exportAsync']
        );
        return $this->success('导出已加入队列');
//        $url = $countSfService->export();
//        return $this->success('导出成功', ['url' => $url]);
    }
}
