<?php

namespace App\Http\Controllers\Api\Mrp;

use App\Exceptions\InvalidRequestException;
use App\Http\Controllers\Controller;
use App\Http\Resources\Api\Mrp\MrpReportStockCountSfResource;
use App\Models\UserImportExportRecord;
use App\Services\ExportService;
use App\Services\Report\MrpReportOrdersSfService;
use App\Services\Report\MrpReportStockCountSfService;
use Illuminate\Http\JsonResponse;

/*MRP(国内)-》MRP SF-》库存统计（HS）*/

class MrpReportStockCountSfController extends Controller
{
    /**
     * 列表
     * @param  MrpReportStockCountSfService  $countSfService
     * @return JsonResponse
     */
    public function list(MrpReportStockCountSfService $countSfService)
    {
        $resource = $countSfService->list();
        return $this->successForResourcePage(MrpReportStockCountSfResource::collection($resource));
    }

    /**
     * 导出
     * @param  MrpReportStockCountSfService  $countSfService
     * @return JsonResponse
     * @throws InvalidRequestException
     */
    public function export(MrpReportStockCountSfService $countSfService)
    {
        (new ExportService())->asyncExport(
            UserImportExportRecord::MODULE_GN_MRP,
            '库存统计(HS).csv',
            [new MrpReportStockCountSfService(), 'exportAsync']
        );
        return $this->success('导出已加入队列');



//        $url = $countSfService->export();
//        return $this->success('导出成功', ['url' => $url]);
    }
}
