<?php

namespace App\Http\Controllers\Api\Mrp;

use App\Exceptions\InvalidRequestException;
use App\Http\Controllers\Controller;
use App\Http\Resources\Api\Mrp\MrpBaseSkuCoreSfResource;
use App\Models\Mrp\MrpBaseSkuCore;
use App\Models\UserImportExportRecord;
use App\Services\ExportService;
use App\Services\Report\MrpBaseSkuCoreSfService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class MrpBaseSkuCoreController extends Controller
{
    /**
     * MRP(国内)-》MRP SF-》备货关系表(HS)列表
     * @param  MrpBaseSkuCoreSfService  $countSfService
     * @return JsonResponse
     */
    public function listSf(MrpBaseSkuCoreSfService $countSfService)
    {
        $resource = $countSfService->list();
        return $this->successForResourcePage(MrpBaseSkuCoreSfResource::collection($resource));
    }

    /**
     * MRP(国内)-》MRP SF-》备货关系表(HS) 导出
     * @param  MrpBaseSkuCoreSfService  $countSfService
     * @return JsonResponse
     * @throws InvalidRequestException
     */
    public function exportSf(MrpBaseSkuCoreSfService $countSfService)
    {
        (new ExportService())->asyncExport(
            UserImportExportRecord::MODULE_GN_MRP,
            '备货关系表(HS).csv',
            [new MrpBaseSkuCoreSfService(), 'export']
        );
        return $this->success('导出已加入队列');
    }

    /**
     * Desc: MRP(国内)-》MRP SF-》备货关系表(HS)
     * @param  Request  $request
     * @return JsonResponse
     */
    public function importSf(Request $request)
    {
        $fileName = $request->input('data.fileName');
        $fileUrl = $request->input('data.fileUrl');

        (new ExportService())->asyncImport(
            UserImportExportRecord::MODULE_GN_MRP,
            $fileName,
            $fileUrl,
            [new MrpBaseSkuCoreSfService(), 'importSfByCsv']
        );
        return $this->success('导入成功', ['url' => $fileUrl]);
    }

    /**
     * MRP(国内)-》MRP V3-》sku对应关系列表V3 列表
     * @param  MrpBaseSkuCoreSfService  $countSfService
     * @return JsonResponse
     */
    public function listV3(MrpBaseSkuCoreSfService $countSfService)
    {
        $resource = $countSfService->list(MrpBaseSkuCore::TYPE_V3);
        return $this->successForResourcePage(MrpBaseSkuCoreSfResource::collection($resource));
    }

    /**
     * MRP(国内)-》MRP V3-》sku对应关系列表V3 导入
     * @param  Request  $request
     * @return JsonResponse
     */
    public function importV3(Request $request)
    {
        $fileName = $request->input('data.fileName');
        $fileUrl = $request->input('data.fileUrl');

        (new ExportService())->asyncImport(
            UserImportExportRecord::MODULE_GN_MRP,
            $fileName,
            $fileUrl,
//            [new MrpBaseSkuCoreSfService(), 'importV3']
            [new MrpBaseSkuCoreSfService(), 'importV3ByCsv']
        );
        return $this->success('导入成功', ['url' => $fileUrl]);
    }

    /**
     * MRP(国内)-》MRP V3-》sku对应关系列表V3  导出
     * @param  MrpBaseSkuCoreSfService  $countSfService
     * @return JsonResponse
     * @throws InvalidRequestException
     */
    public function exportV3(MrpBaseSkuCoreSfService $countSfService)
    {
        (new ExportService())->asyncExport(
            UserImportExportRecord::MODULE_GN_MRP,
            'sku对应关系列表V3.csv',
            [new MrpBaseSkuCoreSfService(), 'exportV3']
        );
        return $this->success('导出已加入队列');

//        $url = $countSfService->exportV3();
//        return $this->success('导出成功', ['url' => $url]);
    }
}
