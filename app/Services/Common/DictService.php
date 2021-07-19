<?php

namespace App\Services\Common;

use App\Services\PrUpload\PoBatchUploadService;
use App\Services\PrUpload\SkuFollowService;
use App\Services\Report\MrpBaseSkuCoreSfService;
use App\Services\Report\MrpResultPlanSfService;
use App\Services\Report\MrpResultPlanV3Service;
use App\Services\User\UserService;

class DictService
{
    /**
     * 获取模块字典
     * @param $type
     * @return array
     * @author jip
     * @time 2020/12/23 19:45
     */
    public function getModuleDict($type)
    {
        $data = [];
        switch ($type) {
            case 'importExportList':
                $data = (new UserService())->getImportExportDict();
                break;
            case 'poBatchUpload':
                $data = (new PoBatchUploadService())->getPoBatchUploadDict();
                break;
            case 'skuFollow':
                $data = (new SkuFollowService())->getSkuFollowDict();
                break;
            case 'mrpBaseSkuCore':
                $data = (new MrpBaseSkuCoreSfService())->getMrpBaseSkuCoreDict();
                break;
            case 'mrpResultPlanSf':
                $data = (new MrpResultPlanSfService())->getMsrpResultPlanSf();
                break;
            case 'mrpResultPlanV3':
                $data = (new MrpResultPlanV3Service())->getMrpResultPlanV3Dict();
                break;

        }
        return $data;
    }
}
