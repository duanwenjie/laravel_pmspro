<?php


namespace App\Http\Controllers\Api\Mrp;

use App\Exceptions\InvalidRequestException;
use App\Http\Controllers\Controller;
use App\Http\Resources\Api\Mrp\MrpReportSalesCountSkuDetailResource;
use App\Models\UserImportExportRecord;
use App\Services\ExportService;
use App\Services\Report\MrpReportSalesCountSkuDetailService;
use Illuminate\Http\JsonResponse;

/*销量源数据*/

class MrpReportSalesCountSkuDetailController extends Controller
{

    /**
     * 列表
     * @param  MrpReportSalesCountSkuDetailService  $befDetailV3Service
     * @return JsonResponse
     */
    public function list(MrpReportSalesCountSkuDetailService $befDetailV3Service)
    {
        $list = $befDetailV3Service->list();
        return $this->successForResourcePage(MrpReportSalesCountSkuDetailResource::collection($list));
    }


    /**
     * 导出
     * @param  MrpReportSalesCountSkuDetailService  $befDetailV3Service
     * @return JsonResponse
     * @throws InvalidRequestException
     */
    public function export(MrpReportSalesCountSkuDetailService $befDetailV3Service)
    {
        /*$url = $befDetailV3Service->export();
        return $this->success('导出成功', ['url' => $url]);*/
        (new ExportService())->asyncExport(
            UserImportExportRecord::MODULE_GN_MRP,
            '销量-SKU明细.csv',
            [new MrpReportSalesCountSkuDetailService(), 'export']
        );
        return $this->success('导出已加入队列');
    }
}
