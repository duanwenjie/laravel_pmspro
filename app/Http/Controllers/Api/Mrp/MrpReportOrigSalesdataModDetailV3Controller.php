<?php


namespace App\Http\Controllers\Api\Mrp;

use App\Exceptions\InvalidRequestException;
use App\Http\Controllers\Controller;
use App\Http\Resources\Api\Mrp\MrpReportOrigSalesdataModDetailV3Resource;
use App\Models\UserImportExportRecord;
use App\Services\ExportService;
use App\Services\Report\MrpReportOrigSalesdataModDetailV3Service;
use Illuminate\Http\JsonResponse;

/*修正前销售明细统计表*/

class MrpReportOrigSalesdataModDetailV3Controller extends Controller
{

    /**
     * 列表
     * @param  MrpReportOrigSalesdataModDetailV3Service  $befDetailV3Service
     * @return JsonResponse
     */
    public function list(MrpReportOrigSalesdataModDetailV3Service $befDetailV3Service)
    {
        $list = $befDetailV3Service->list();
        return $this->successForResourcePage(MrpReportOrigSalesdataModDetailV3Resource::collection($list));
    }


    /**
     * 导出
     * @param  MrpReportOrigSalesdataModDetailV3Service  $befDetailV3Service
     * @return JsonResponse
     * @throws InvalidRequestException
     */
    public function export(MrpReportOrigSalesdataModDetailV3Service $befDetailV3Service)
    {
        /*$url = $befDetailV3Service->export();
        return $this->success('导出成功', ['url' => $url]);*/
        (new ExportService())->asyncExport(
            UserImportExportRecord::MODULE_GN_MRP,
            '修正前销售明细统计表.csv',
            [new MrpReportOrigSalesdataModDetailV3Service(), 'export']
        );
        return $this->success('导出已加入队列');
    }
}
