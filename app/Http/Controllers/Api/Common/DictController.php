<?php


namespace App\Http\Controllers\Api\Common;

use App\Http\Controllers\Controller;
use App\Services\Common\DictService;
use Illuminate\Http\JsonResponse;

class DictController extends Controller
{
    /**
     * 获取模块字典
     * @return JsonResponse
     * @author jip
     * @time 2020/12/23 19:47
     */
    public function index()
    {
        $type = request()->input('data.type');
        $data = (new DictService())->getModuleDict($type);
        foreach ($data as &$v) {
            array_unshift($v, ['code' => '', 'name' => '全部']);
        }
        return $this->success('', $data);
    }
}
