<?php


namespace App\Http\Controllers\Api\Mrp;

use App\Exceptions\InvalidRequestException;
use App\Http\Controllers\Controller;
use App\Http\Resources\Api\Mrp\MrpReportSalesCountPlatformAllResource;
use App\Models\UserImportExportRecord;
use App\Services\ExportService;
use App\Services\Report\MrpReportSalesCountPlatformAllService;
use Illuminate\Http\JsonResponse;

//MRP(国内)-》平台+SKU销量统计(不剔除)
class MrpReportSalesCountPlatformAllController extends Controller
{

    /**
     * 列表
     * @param  MrpReportSalesCountPlatformAllService  $befDetailV3Service
     * @return JsonResponse
     */
    public function list(MrpReportSalesCountPlatformAllService $befDetailV3Service)
    {
        $list = $befDetailV3Service->list();
        return $this->successForResourcePage(MrpReportSalesCountPlatformAllResource::collection($list));
    }


    /**
     * 导出
     * @param  MrpReportSalesCountPlatformAllService  $befDetailV3Service
     * @return JsonResponse
     * @throws InvalidRequestException
     */
    public function export(MrpReportSalesCountPlatformAllService $befDetailV3Service)
    {
        /*$url = $befDetailV3Service->export();
        return $this->success('导出成功', ['url' => $url]);*/
        (new ExportService())->asyncExport(
            UserImportExportRecord::MODULE_GN_MRP,
            '平台+SKU销量统计(不剔除).csv',
            [new MrpReportSalesCountPlatformAllService(), 'export']
        );
        return $this->success('导出已加入队列');
    }
}
