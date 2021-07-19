<?php
/**
 * 撤销在途功能
 * User: jiangshilin(jiangshilin@youkeshu.com)
 * Date: 2021/7/1
 * Time: 10:31
 */


namespace App\Http\Controllers\Api\Mrp;


use App\Http\Controllers\Controller;
use App\Http\Requests\Api\Mrp\MrpRequest;
use App\Http\Resources\Api\Mrp\CancelpoResource;
use App\Services\Report\CancelpoService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class CancelpoController extends Controller
{
    /**
     * 撤销在途-》撤销在途列表
     */
    public function getOrderLists(CancelpoService $cancelpoService){
        $lists = $cancelpoService->getOrderLists();
        return $this->successForResourcePage(CancelpoResource::collection($lists));
    }

    /**
     * 撤销在途-》撤销在途列表-》导出
     */
    public function exportOrderLists(CancelpoService $cancelpoService){
        $url =  $cancelpoService->exportOrderLists();
        return $this->success('导出成功', ['url' => $url]);
    }

    /**
     * 撤销在途-》撤销在途列表-》计算
     */
    public function operateOrderLists(CancelpoService $cancelpoService,MrpRequest $request){
        $cancelpoService->operateOrderLists($request);
        return $this->success('计算成功');
    }

    /**
     * 撤销在途-》撤销总量计算表
     */
    public function getTotalResultLists(CancelpoService $cancelpoService){
        $lists = $cancelpoService->getTotalResultLists();
        return $this->successForResourcePage(CancelpoResource::collection($lists));
    }

    /**
     * 撤销在途-》撤销总量计算-》导出
     */
    public function exportTotalResultLists(CancelpoService $cancelpoService){
        $url =  $cancelpoService->exportTotalResultLists();
        return $this->success('导出成功', ['url' => $url]);
    }

    /**
     * 撤销在途-》撤销明细表-》列表
     */
    public function getDetaillResultLists(CancelpoService $cancelpoService){
        $lists = $cancelpoService->getDetaillResultLists();
        return $this->successForResourcePage(CancelpoResource::collection($lists));
    }

    /**
     * 撤销在途-》撤销明细表-》导出
     */
    public function exportDetaillResultLists(CancelpoService $cancelpoService){
        $url =  $cancelpoService->exportDetaillResultLists();
        return $this->success('导出成功', ['url' => $url]);
    }

    /**
     * 撤销在途-》撤销明细表-》上传
     */
    public function uploadDetaillResultLists(CancelpoService $cancelpoService,Request $request){
        set_time_limit(3600);
        ini_set('memory_limit', '2048M');
        $fileName = $request->input('data.fileName');
        $fileUrl = $request->input('data.fileUrl');
        Storage::disk('export')->put($fileName, file_get_contents($fileUrl));
        $filePath = file_save_path($fileName, 'export');
        $cancelpoService->uploadDetaillResultLists($filePath);
        Storage::disk('export')->delete($fileName);
        return $this->success('导入成功');
    }


}
