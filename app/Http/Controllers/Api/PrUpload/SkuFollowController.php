<?php
/**
 * Description
 * User: dwj
 * Date: 2021/5/28
 * Time: 2:57 下午
 */

namespace App\Http\Controllers\Api\PrUpload;

use App\Exceptions\InvalidRequestException;
use App\Http\Controllers\Controller;
use App\Http\Resources\Api\PrUpload\SkuFollowResource;
use App\Services\PrUpload\SkuFollowService;
use Illuminate\Http\JsonResponse;

class SkuFollowController extends Controller
{
    /**
     * 列表
     * @param  SkuFollowService  $service
     * @return JsonResponse
     * @author dwj
     */
    public function list(SkuFollowService $service)
    {
        $resource = $service->getSkuFollowList();
        return $this->successForResourcePage(SkuFollowResource::collection($resource));
    }

    /**
     * 列表导出
     * @param  SkuFollowService  $service
     * @return JsonResponse
     * @throws InvalidRequestException
     * @author dwj
     */
    public function exportList(SkuFollowService $service)
    {
        $url = $service->exportSkuFollowList();
        return $this->success('导出成功', ['url' => $url]);
    }
}
