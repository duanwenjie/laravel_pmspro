<?php

namespace App\Http\Controllers\Api\PrUpload;

use App\Exceptions\InvalidRequestException;
use App\Http\Controllers\Api\LocalService\DingFacadeService;
use App\Http\Controllers\Api\LocalService\SingletonService;
use App\Http\Controllers\Controller;
use App\Http\Requests\PrUpload\PrUploadRequest;
use App\Http\Resources\Api\PrUpload\PoBatchUploadResource;
use App\Imports\PrUpload\PoBatchUploadImportSave;
use App\Services\PrUpload\PoBatchUploadService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;

class PoBatchUploadController extends Controller
{
    /**
     * PR单批量上传
     * @param  Request  $request
     * @return JsonResponse
     * @author dwj
     */
    public function import(PrUploadRequest $request)
    {
        set_time_limit(3600);
        ini_set('memory_limit', '2048M');

        $fileName = $request->input('data.fileName');
        $fileUrl = $request->input('data.fileUrl');

        // 异步导入
        //(new ExportService())->asyncImport(
        //    UserImportExportRecord::MODULE_GN_PR_BATCH_UPLOAD,
        //    $fileName,
        //    $fileUrl,
        //    [new PoBatchUploadService(), 'import']
        //);

        // 改为同步导入
        Storage::disk('export')->put($fileName, file_get_contents($fileUrl));
        $filePath = file_save_path($fileName, 'export');
        (new PoBatchUploadService())->import($filePath);
        Storage::disk('export')->delete($fileName);

        return $this->success('导入成功', ['url' => $fileUrl]);
    }

    /**
     * 列表
     * @param  PoBatchUploadService  $service
     * @return JsonResponse
     * @author dwj
     */
    public function list(PoBatchUploadService $service)
    {
        return $this->successForResourcePage(PoBatchUploadResource::collection($service->getList()));
    }

    /**
     * 列表导出
     * @param  PoBatchUploadService  $service
     * @return JsonResponse
     * @throws InvalidRequestException
     * @author dwj
     */
    public function exportList(PoBatchUploadService $service)
    {
        $url = $service->exportList();
        return $this->success('导出成功', ['url' => $url]);
    }

    /**
     * 列表保存（批量）
     * @param  PrUploadRequest  $request
     * @param  PoBatchUploadService  $service
     * @return JsonResponse
     * @throws InvalidRequestException
     * @author dwj
     */
    public function save(PrUploadRequest $request, PoBatchUploadService $service)
    {
        return $this->success($service->saveOrCancel($request));
    }

    /**
     * 导入批量修改PR信息
     * @param PrUploadRequest $request
     * @return JsonResponse
     * @author dwj
     */
    public function importSave(PrUploadRequest $request)
    {
        //DingFacadeService::info('XXXXEEEEEE');

        $instance = SingletonService::getInstance();
        $res = $instance->test();
        print_r($res);print_r('<br>');exit();

        //set_time_limit(3600);
        //ini_set('memory_limit', '2048M');
        //
        //$fileName = $request->input('data.fileName');
        //$fileUrl = $request->input('data.fileUrl');
        //
        //// 同步导入
        //Storage::disk('export')->put($fileName, file_get_contents($fileUrl));
        //$filePath = file_save_path($fileName, 'export');
        //(new PoBatchUploadImportSave())->import($filePath);
        //Storage::disk('export')->delete($fileName);
        //
        //return $this->success('导入成功', ['url' => $fileUrl]);
    }

    /**
     * PR单列表撤销/撤销并释放PR（批量）
     * @param  PrUploadRequest  $request
     * @param  PoBatchUploadService  $service
     * @return JsonResponse
     * @throws InvalidRequestException
     * @author dwj
     */
    public function cancel(PrUploadRequest $request, PoBatchUploadService $service)
    {
        return $this->success($service->saveOrCancel($request, 2));
    }

    /**
     * PR单列表批量更新异常
     * @param  PrUploadRequest  $request
     * @param  PoBatchUploadService  $service
     * @return JsonResponse
     * @author dwj
     */
    public function updateError(PrUploadRequest $request, PoBatchUploadService $service)
    {
        return $this->success($service->updateError($request));
    }
}
