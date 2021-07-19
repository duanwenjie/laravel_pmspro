<?php

namespace App\Http\Controllers\Api\Mrp;

use App\Exceptions\InvalidRequestException;
use App\Http\Controllers\Controller;
use App\Http\Resources\Api\Mrp\MrpReportOrdersSfResource;
use App\Models\UserImportExportRecord;
use App\Services\ExportService;
use App\Services\Report\MrpBaseSkuCoreSfService;
use App\Services\Report\MrpReportOrdersSfService;
use Illuminate\Http\JsonResponse;

/*MRP(国内)-》MRP SF-》销量统计（HS）*/

class MrpReportOrdersSfController extends Controller
{
    /**
     * 列表
     * @param  MrpReportOrdersSfService  $befDetailV3Service
     * @return JsonResponse
     */
    public function list(MrpReportOrdersSfService $befDetailV3Service)
    {
        $resource = $befDetailV3Service->list();
        return $this->successForResourcePage(MrpReportOrdersSfResource::collection($resource));
    }

    /**
     * 导出
     * @param  MrpReportOrdersSfService  $befDetailV3Service
     * @return JsonResponse
     * @throws InvalidRequestException
     */
    public function export(MrpReportOrdersSfService $befDetailV3Service)
    {
        (new ExportService())->asyncExport(
            UserImportExportRecord::MODULE_GN_MRP,
            '销量统计(HS).csv',
            [new MrpReportOrdersSfService(), 'exportAsync']
        );
        return $this->success('导出已加入队列');

//        $url = $befDetailV3Service->export();
//        return $this->success('导出成功', ['url' => $url]);
    }
}
