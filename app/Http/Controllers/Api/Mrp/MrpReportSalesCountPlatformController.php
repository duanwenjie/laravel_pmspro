<?php


namespace App\Http\Controllers\Api\Mrp;

use App\Exceptions\InvalidRequestException;
use App\Http\Controllers\Controller;
use App\Http\Resources\Api\Mrp\MrpReportSalesCountPlatformResource;
use App\Models\UserImportExportRecord;
use App\Services\ExportService;
use App\Services\Report\MrpReportSalesCountPlatformService;
use Illuminate\Http\JsonResponse;

/*销量源数据*/

class MrpReportSalesCountPlatformController extends Controller
{

    /**
     * 列表
     * @param  MrpReportSalesCountPlatformService  $befDetailV3Service
     * @return JsonResponse
     */
    public function list(MrpReportSalesCountPlatformService $befDetailV3Service)
    {
        $list = $befDetailV3Service->list();
        return $this->successForResourcePage(MrpReportSalesCountPlatformResource::collection($list));
    }


    /**
     * 导出
     * @param  MrpReportSalesCountPlatformService  $befDetailV3Service
     * @return JsonResponse
     * @throws InvalidRequestException
     */
    public function export(MrpReportSalesCountPlatformService $befDetailV3Service)
    {
        /*$url = $befDetailV3Service->export();
        return $this->success('导出成功', ['url' => $url]);*/
        (new ExportService())->asyncExport(
            UserImportExportRecord::MODULE_GN_MRP,
            '平台+SKU销量统计.csv',
            [new MrpReportSalesCountPlatformService(), 'export']
        );
        return $this->success('导出已加入队列');
    }
}
