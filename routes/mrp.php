<?php
$router = app('router');
$router->namespace('App\Http\Controllers\Api')->group(function (\Illuminate\Routing\Router $router) {
    $router->group(['prefix'=>'Mrp','namespace'=>'Mrp'], function ($router) {

        //MRP(国内)-》MRP V3-》销量源数据
        $router->post('mrpReportOrigSalesdataV3List', 'MrpReportOrigSalesdataV3Controller@list')->name(\App\Tools\RouteName::MRP_REPORT_ORIG_SALESDATA_V3_LIST);
        $router->post('mrpReportOrigSalesdataV3Export', 'MrpReportOrigSalesdataV3Controller@export')->name(\App\Tools\RouteName::MRP_REPORT_ORIG_SALESDATA_V3_EXPORT);

        //MRP(国内)-》MRP V3-》销量源数据（修正后）
        $router->post('mrpReportOrigSalesdataModV3List', 'MrpReportOrigSalesdataModV3Controller@list')->name(\App\Tools\RouteName::MRP_REPORT_ORIG_SALESDATA_MOD_V3_LIST);
        $router->post('mrpReportOrigSalesdataModV3Export', 'MrpReportOrigSalesdataModV3Controller@export')->name(\App\Tools\RouteName::MRP_REPORT_ORIG_SALESDATA_MOD_V3_EXPORT);

        //MRP(国内)-》MRP V3-》修正前销售明细统计表
        $router->post('mrpReportOrigSalesdataModDetailV3List', 'MrpReportOrigSalesdataModDetailV3Controller@list')->name(\App\Tools\RouteName::MRP_REPORT_ORIG_SALESDATA_MOD_DETAIL_V3_LIST);
        $router->post('mrpReportOrigSalesdataModDetailV3Export', 'MrpReportOrigSalesdataModDetailV3Controller@export')->name(\App\Tools\RouteName::MRP_REPORT_ORIG_SALESDATA_MOD_DETAIL_V3_EXPORT);


        //MRP(国内)-》MRP V3-》修正后销售明细统计表
        $router->post('mrpReportOrdersModBefDetailV3List', 'MrpReportOrdersModBefDetailV3Controller@list')->name(\App\Tools\RouteName::MRP_REPORT_ORDERS_MOD_BEF_DETAIL_V3_LIST);
        $router->post('mrpReportOrdersModBefDetailV3Export', 'MrpReportOrdersModBefDetailV3Controller@export')->name(\App\Tools\RouteName::MRP_REPORT_ORDERS_MOD_BEF_DETAIL_V3_EXPORT);

        ///MRP(国内)-》sku日均销量统计报表
        $router->post('mrpReportDaySalesCountList', 'MrpReportDaySalesCountController@list')->name(\App\Tools\RouteName::MRP_REPORT_DAY_SALES_COUNT_LIST);
        $router->post('mrpReportDaySalesCountExport', 'MrpReportDaySalesCountController@export')->name(\App\Tools\RouteName::MRP_REPORT_DAY_SALES_COUNT_EXPORT);

        ///MRP(国内)-》日缺货率统计报表
        $router->post('mrpReportOosOrdersDList', 'MrpReportOosOrdersDController@list')->name(\App\Tools\RouteName::MRP_REPORT_OOS_ORDERS_D_LIST);
        $router->post('mrpReportOosOrdersDExport', 'MrpReportOosOrdersDController@export')->name(\App\Tools\RouteName::MRP_REPORT_OOS_ORDERS_D_EXPORT);

        ///MRP(国内)-》周缺货率统计报表
        $router->post('mrpReportOosOrdersWList', 'MrpReportOosOrdersWController@list')->name(\App\Tools\RouteName::MRP_REPORT_OOS_ORDERS_W_LIST);
        $router->post('mrpReportOosOrdersWExport', 'MrpReportOosOrdersWController@export')->name(\App\Tools\RouteName::MRP_REPORT_OOS_ORDERS_W_EXPORT);


        ///MRP(国内)-》月缺货率统计报表
        $router->post('mrpReportOosOrdersMList', 'MrpReportOosOrdersMController@list')->name(\App\Tools\RouteName::MRP_REPORT_OOS_ORDERS_M_LIST);
        $router->post('mrpReportOosOrdersMExport', 'MrpReportOosOrdersMController@export')->name(\App\Tools\RouteName::MRP_REPORT_OOS_ORDERS_M_EXPORT);


        ///MRP(国内)-》平台+SKU销量统计
        $router->post('mrpReportSalesCountPlatformList', 'MrpReportSalesCountPlatformController@list')->name(\App\Tools\RouteName::MRP_REPORT_SALES_COUNT_PLATFORM_LIST);
        $router->post('mrpReportSalesCountPlatformExport', 'MrpReportSalesCountPlatformController@export')->name(\App\Tools\RouteName::MRP_REPORT_SALES_COUNT_PLATFORM_EXPORT);

        ///MRP(国内)-》MRP V3-》销量明细统计表(剔除海狮，BB业务线)
        $router->post('mrpReportOrigSalesdataDetailV3NewList', 'MrpReportOrigSalesdataDetailV3NewController@list')->name(\App\Tools\RouteName::MRP_REPORT_ORIG_SALESDATA_DETAIL_V3_NEW_LIST);
        $router->post('mrpReportOrigSalesdataDetailV3NewExport', 'MrpReportOrigSalesdataDetailV3NewController@export')->name(\App\Tools\RouteName::MRP_REPORT_ORIG_SALESDATA_DETAIL_V3_NEW_EXPORT);

        ///MRP(国内)-》MRP V3-》销量-SKU明细
        $router->post('mrpReportSalesCountSkuDetailList', 'MrpReportSalesCountSkuDetailController@list')->name(\App\Tools\RouteName::MRP_REPORT_SALES_COUNT_SKU_DETAIL_LIST);
        $router->post('mrpReportSalesCountSkuDetailExport', 'MrpReportSalesCountSkuDetailController@export')->name(\App\Tools\RouteName::MRP_REPORT_SALES_COUNT_SKU_DETAIL_EXPORT);

        ///MRP(国内)-》MRP V3-》销量明细统计表
        $router->post('mrpReportOrigSalesdataDetailV3List', 'MrpReportOrigSalesdataDetailV3Controller@list')->name(\App\Tools\RouteName::MRP_REPORT_ORIG_SALESDATA_DETAIL_V3_LIST);
        $router->post('mrpReportOrigSalesdataDetailV3Export', 'MrpReportOrigSalesdataDetailV3Controller@export')->name(\App\Tools\RouteName::MRP_REPORT_ORIG_SALESDATA_DETAIL_V3_EXPORT);


        ///MRP(国内)-》MRP V3-》计算SKU自动补货
        $router->post('mrpResultPlanV3List', 'MrpResultPlanV3Controller@list')->name(\App\Tools\RouteName::MRP_RESULT_PLAN_V3_LIST);
        $router->post('mrpResultPlanV3Export', 'MrpResultPlanV3Controller@export')->name(\App\Tools\RouteName::MRP_RESULT_PLAN_V3_EXPORT);


        //MRP(国内)-》每日最新缺货占比统计报表
        $router->post('mrpReportOosOrdersDV2List', 'MrpReportOosOrdersDV2Controller@list')->name(\App\Tools\RouteName::MRP_REPORT_OOS_ORDERS_D_V2_LIST);
        $router->post('mrpReportOosOrdersDV2Export', 'MrpReportOosOrdersDV2Controller@export')->name(\App\Tools\RouteName::MRP_REPORT_OOS_ORDERS_D_V2_EXPORT);

        //MRP(国内)-》撤单和缺货订单日统计
        $router->post('mrpReportOosOrdersDAllV2List', 'MrpReportOosOrdersDAllV2Controller@list')->name(\App\Tools\RouteName::MRP_REPORT_OOS_ORDERS_D_ALL_V2_LIST);
        $router->post('mrpReportOosOrdersDAllV2Export', 'MrpReportOosOrdersDAllV2Controller@export')->name(\App\Tools\RouteName::MRP_REPORT_OOS_ORDERS_D_ALL_V2_EXPORT);


        //MRP(国内)-》每日缺货订单明细
        $router->post('mrpReportOosOrdersDetailDailyList', 'MrpReportOosOrdersDetailDailyController@list')->name(\App\Tools\RouteName::MRP_REPORT_OOS_ORDERS_DETAIL_DAILY_LIST);
        $router->post('mrpReportOosOrdersDetailDailyExport', 'MrpReportOosOrdersDetailDailyController@export')->name(\App\Tools\RouteName::MRP_REPORT_OOS_ORDERS_DETAIL_DAILY_EXPORT);



        //MRP(国内)-》平台+SKU销量统计(不剔除)
        $router->post('mrpReportSalesCountPlatformAllList', 'MrpReportSalesCountPlatformAllController@list')->name(\App\Tools\RouteName::MRP_REPORT_SALES_COUNT_PLATFORM_ALL_LIST);
        $router->post('mrpReportSalesCountPlatformAllExport', 'MrpReportSalesCountPlatformAllController@export')->name(\App\Tools\RouteName::MRP_REPORT_SALES_COUNT_PLATFORM_ALL_EXPORT);


        //MRP(国内)-》MRP V3-》SKU日销量统计（修正前）
        $router->post('mrpReportOrdersModBefV3List', 'MrpReportOrdersModBefV3Controller@list')->name(\App\Tools\RouteName::ROUTE_MRP_SKU_ORDERS_MOD_BEF_V3_LIST);
        $router->post('mrpReportOrdersModBefV3Export', 'MrpReportOrdersModBefV3Controller@export')->name(\App\Tools\RouteName::ROUTE_MRP_SKU_ORDERS_MOD_BEF_V3_EXPORT);

        //MRP(国内)-》MRP V3-》SKU销量统计（修正后）
        $router->post('mrpReportOrdersModAftV3List', 'MrpReportOrdersModAftV3Controller@list')->name(\App\Tools\RouteName::ROUTE_MRP_REPORT_ORDERS_MOD_AFT_V3_LIST);
        $router->post('mrpReportOrdersModAftV3Export', 'MrpReportOrdersModAftV3Controller@export')->name(\App\Tools\RouteName::ROUTE_MRP_REPORT_ORDERS_MOD_AFT_V3_EXPORT);

        //MRP(国内)-》MRP V3-》SKU库存统计
        $router->post('mrpReportStockCountV3List', 'MrpReportStockCountV3Controller@list')->name(\App\Tools\RouteName::ROUTE_MRP_REPORT_STOCK_COUNT_V3_LIST);
        $router->post('mrpReportStockCountV3Export', 'MrpReportStockCountV3Controller@export')->name(\App\Tools\RouteName::ROUTE_MRP_REPORT_STOCK_COUNT_V3_EXPORT);

        //MRP(国内)-》总缺货订单明细
        $router->post('mrpReportOosOrdersDetailTotalList', 'MrpReportOosOrdersDetailTotalController@list')->name(\App\Tools\RouteName::ROUTE_MRP_REPORT_OOS_ORDERS_DETAIL_TOTAL_LIST);
        $router->post('mrpReportOosOrdersDetailTotalExport', 'MrpReportOosOrdersDetailTotalController@export')->name(\App\Tools\RouteName::ROUTE_MRP_REPORT_OOS_ORDERS_DETAIL_TOTAL_EXPORT);

        //MRP(国内)-》MRP SF-》销量源数据（HS）
        $router->post('mrpReportOrigSalesdataSfList', 'MrpReportOrigSalesdataSfController@list')->name(\App\Tools\RouteName::MRP_MRP_REPORT_ORIG_SALESDATA_SF_LIST);
        $router->post('mrpReportOrigSalesdataSfExport', 'MrpReportOrigSalesdataSfController@export')->name(\App\Tools\RouteName::MRP_MRP_REPORT_ORIG_SALESDATA_SF_EXPORT);

        //MRP(国内)-》MRP SF-》销量统计（HS）
        $router->post('mrpReportOrdersSfList', 'MrpReportOrdersSfController@list')->name(\App\Tools\RouteName::MRP_MRP_REPORT_ORDERS_SF_LIST);
        $router->post('mrpReportOrdersSfExport', 'MrpReportOrdersSfController@export')->name(\App\Tools\RouteName::MRP_MRP_REPORT_ORDERS_SF_EXPORT);

        //MRP(国内)-》MRP SF-》自动补货建议（HS）
        $router->post('mrpResultPlanSfList', 'MrpResultPlanSfController@list')->name(\App\Tools\RouteName::MRP_MRP_RESULT_PLAN_SF_LIST);
        $router->post('mrpResultPlanSfExport', 'MrpResultPlanSfController@export')->name(\App\Tools\RouteName::MRP_MRP_RESULT_PLAN_SF_EXPORT);

        //MRP(国内)-》MRP SF-》库存统计（HS）
        $router->post('mrpReportStockCountSfList', 'MrpReportStockCountSfController@list')->name(\App\Tools\RouteName::MRP_MRP_REPORT_STOCK_COUNT_SF_LIST);
        $router->post('mrpReportStockCountSfExport', 'MrpReportStockCountSfController@export')->name(\App\Tools\RouteName::MRP_MRP_REPORT_STOCK_COUNT_SF_EXPORT);

        //MRP(国内)-》MRP SF-》shopify平台sku日销量统计报表
        $router->post('mrpReportDaySalesCountSfList', 'MrpReportDaySalesCountSfController@list')->name(\App\Tools\RouteName::MRP_MRP_REPORT_DAY_SALES_COUNT_SF_LIST);
        $router->post('mrpReportDaySalesCountSfExport', 'MrpReportDaySalesCountSfController@export')->name(\App\Tools\RouteName::MRP_MRP_REPORT_DAY_SALES_COUNT_SF_EXPORT);

        //MRP(国内)-》MRP SF-》备货关系表(HS) && V3

        $router->post('mrpBaseSkuCoreListSf', 'MrpBaseSkuCoreController@listSf')->name(\App\Tools\RouteName::MRP_MRP_BASE_SKU_CORE_SF_LIST);
        $router->post('mrpBaseSkuCoreExportSf', 'MrpBaseSkuCoreController@exportSf')->name(\App\Tools\RouteName::MRP_MRP_BASE_SKU_CORE_SF_EXPORT);
        $router->post('mrpBaseSkuCoreImportSf', 'MrpBaseSkuCoreController@importSf')->name(\App\Tools\RouteName::MRP_MRP_BASE_SKU_CORE_SF_IMPORT);

        $router->post('mrpBaseSkuCoreListV3', 'MrpBaseSkuCoreController@listV3')->name(\App\Tools\RouteName::MRP_MRP_BASE_SKU_CORE_V3_LIST);
        $router->post('mrpBaseSkuCoreExportV3', 'MrpBaseSkuCoreController@exportV3')->name(\App\Tools\RouteName::MRP_MRP_BASE_SKU_CORE_V3_EXPORT);
        $router->post('mrpBaseSkuCoreImportV3', 'MrpBaseSkuCoreController@importV3')->name(\App\Tools\RouteName::MRP_MRP_BASE_SKU_CORE_V3_IMPORT);


        //MRP(国内)-》历史每日缺货占比统计表
        $router->post('mrpReportOosOrdersDHisV2List', 'MrpReportOosOrdersDHisV2Controller@list')->name(\App\Tools\RouteName::MRP_REPORT_OOS_ORDERS_D_HIS_V2_LIST);
        $router->post('mrpReportOosOrdersDHisV2Export', 'MrpReportOosOrdersDHisV2Controller@export')->name(\App\Tools\RouteName::MRP_REPORT_OOS_ORDERS_D_HIS_V2_EXPORT);

        //MRP(国内)-》MRP V3-》销量-SKU统计
        $router->post('mrpReportSalesCountSkuList', 'MrpReportSalesCountSkuController@list')->name(\App\Tools\RouteName::MRP_REPORT_SALES_COUNT_SKU_LIST);
        $router->post('mrpReportSalesCountSkuExport', 'MrpReportSalesCountSkuController@export')->name(\App\Tools\RouteName::MRP_REPORT_SALES_COUNT_SKU_EXPORT);
    });

    $router->group(['prefix'=>'Cancelpo','namespace'=>'Mrp'], function ($router) {
        //撤销在途-》撤销在途列表
        $router->any('getOrderLists', 'CancelpoController@getOrderLists')->name(\App\Tools\RouteName::CANCELPO_GET_ORDER_LISTS);
        $router->any('exportOrderLists', 'CancelpoController@exportOrderLists')->name(\App\Tools\RouteName::CANCELPO_EXPORT_ORDER_LISTS);
        $router->any('operateOrderLists', 'CancelpoController@operateOrderLists')->name(\App\Tools\RouteName::CANCELPO_OPERATE_ORDER_LISTS);
        //撤销在途-》撤销总量计算表
        $router->any('getTotalResultLists', 'CancelpoController@getTotalResultLists')->name(\App\Tools\RouteName::CANCELPO_GET_TOTAL_RESULT_LISTS);
        $router->any('exportTotalResultLists', 'CancelpoController@exportTotalResultLists')->name(\App\Tools\RouteName::CANCELPO_EXPORT_TOTAL_RESULT_LISTS);
        //撤销在途-》撤销明细表
        $router->any('getDetaillResultLists', 'CancelpoController@getDetaillResultLists')->name(\App\Tools\RouteName::CANCELPO_GET_DETAIL_RESULT_LISTS);
        $router->any('exportDetaillResultLists', 'CancelpoController@exportDetaillResultLists')->name(\App\Tools\RouteName::CANCELPO_EXPORT_DETAIL_RESULT_LISTS);
        $router->any('uploadDetaillResultLists', 'CancelpoController@uploadDetaillResultLists')->name(\App\Tools\RouteName::CANCELPO_UPLOAD_DETAIL_RESULT_LISTS);


    });
});
