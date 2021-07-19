<?php


return [
    //文件系统
    'yks_file_system' => [
        'production' => 'http://soter.youkeshu.com/yks/file/server/',
        'preview' => 'http://soter.youkeshu.com/yks/file/server/',
        'test' => 'http://10.90.1.204:8000/yks/file/server/',
        'local' => 'http://10.90.1.204:8000/yks/file/server/',

    ],
    //基础资料库
    'sku_system' => [
        'production' => 'http://skuapi.kokoerp.com/',
        'preview' => 'http://skuapi.kokoerp.com/',
        'test' => 'http://skuapi.kokoerp.com/',
        'local' => 'http://skuapi.kokoerp.com/',
    ],
    //图库
    'tuku_url'=>[
        'production' => 'http://192.168.5.207:9991/',
        'preview' => 'http://192.168.5.207:9991/',
        'test' => 'http://192.168.5.207:9991/',
        'local' => 'http://192.168.5.207:9991/',
    ],
    //OMS HTTP
    'oms_http'=>[
        'production' => 'http://newomsweb.kokoerp.com/',
        'preview' => 'http://newomsweb.kokoerp.com/',
        'test' => 'http://newomsweb.kokoerp.com/',
        'local' => 'http://newomsweb.kokoerp.com/',
    ],
    //新帐号管理系统RPC
    'iactmgr_rpc'=>[
        'production' => 'motan2://10.90.1.187:9006/com.yks.actmgr.motan.service.api.IActMgrService?group=rpc-service-group-pro',
        'preview' => 'motan2://127.0.0.1:10315/com.yks.actmgr.motan.service.api.IActMgrService?group=rpc-service-group-test',
        'test' => 'motan2://127.0.0.1:10315/com.yks.actmgr.motan.service.api.IActMgrService?group=rpc-service-group-test',
        'local' => 'motan2://172.36.10.63:8002/com.yks.actmgr.motan.service.api.IActMgrService?group=rpc-service-group-pro',
    ],
    //fbaerp新品
    'ipimfba_rpc'=>[
        'production' => 'motan2://127.0.0.1:10315/com.yks.pim.motan.service.api.IPimFbaService?group=rpc-service-group-pro',
        'preview' => 'motan2://127.0.0.1:10315/com.yks.pim.motan.service.api.IPimFbaService?group=rpc-service-group-test',
        'test' => 'motan2://127.0.0.1:10315/com.yks.pim.motan.service.api.IPimFbaService?group=rpc-service-group-test',
        'local' => 'motan2://10.90.1.204:9000/com.yks.pim.motan.service.api.IPimFbaService?group=rpc-service-group-test',
    ],
    //刊登系统
    'iplsfacade_rpc'=>[
        'production' => 'motan2://127.0.0.1:10315/com.yks.pls.motan.service.api.IPlsFacadeService?group=rpc-service-group-pro',
        'preview' => 'motan2://127.0.0.1:10315/com.yks.pls.motan.service.api.IPlsFacadeService?group=rpc-service-group-test',
        'test' => 'motan2://127.0.0.1:10315/com.yks.pls.motan.service.api.IPlsFacadeService?group=rpc-service-group-test',
        'local' => 'motan2://172.36.18.200:8002/com.yks.pls.motan.service.api.IPlsFacadeService?group=rpc-service-group-pro',
    ],
    //全球仓储
    'overseawms' => [
        'production' => 'http://overseawms.kokoerp.com/',
        'preview' => 'http://test.overseawms.kokoerp.com/',
        'test' => 'http://test.overseawms.kokoerp.com/',
        'local' => 'http://test.overseawms.kokoerp.com/',
    ],
    //FBA中转仓
    'fbawarehouse' => [
        'production' => 'http://fbawarehouse.youkeshu.com/index.php/',
        'preview' => 'http://test.fbawarehouse.kokoerp.com/index.php/',
        'test' => 'http://test.fbawarehouse.kokoerp.com/index.php/',
        'local' => 'http://test.fbawarehouse.kokoerp.com/index.php/',
    ],
    //美2仓
    'wmsus2'=>[
        'production' => 'http://wms4us2.youkeshu.com/',
        'preview' => 'http://wms4us2.youkeshu.com/',
        'test' => 'http://wms4us2.youkeshu.com/',
        'local' => 'http://wms4us2.youkeshu.com/',
    ],
    //杜伊斯堡仓库
    'duisburg'=>[
        'production' => 'http://main.transfer.dolphinsc.com/',
        'preview' => 'http://main.transfer.dolphinsc.com/',
        'test' => 'http://main.transfer.dolphinsc.com/',
        'local' => 'http://main.transfer.dolphinsc.com/',
    ],
    //NEWWMS
    'newwms' => [
        'production' => 'http://newwms.kokoerp.com/api/',
        'preview' => 'http://test.newwms.kokoerp.com/api/',
        'test' => 'http://test.newwms.kokoerp.com/api/',
        'local' => 'http://test.newwms.kokoerp.com/api/',
    ],
    'ysk_hr'=>[
        'production' => 'http://ykshr.kokoerp.com/',
        'preview' => 'http://test.ykshr.kokoerp.com/',
        'test' => 'http://test.ykshr.kokoerp.com/',
        'local' => 'http://test.ykshr.kokoerp.com/',
    ],
    //海外中转仓
    'omltransport'=>[
        'production' => 'http://192.168.5.42:8180/',
        'preview' => 'http://test.outmainlinetransport.kokoerp.com/',
        'test' => 'http://test.outmainlinetransport.kokoerp.com/',
        'local' => 'http://test.outmainlinetransport.kokoerp.com/',
    ],
    'ysk_user_info'=>[
        'production' => 'https://userinfo.youkeshu.com',
        'preview' => 'https://userinfo.youkeshu.com',
        'test' => 'https://userinfo.youkeshu.com',
        'local' => 'https://userinfo.youkeshu.com',
    ],
    'pms'=>[
        'production' => 'http://ykspms.kokoerp.com/',
        'preview' => 'http://ykspms.kokoerp.com/',
        'test' => 'http://test.ykspms.kokoerp.com/',
        'local' => 'http://www.ykspms.com/',
    ],
];
