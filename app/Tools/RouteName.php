<?php


namespace App\Tools;

class RouteName
{
    //用户导入导出列表
    const ROUTE_USER_IMPORT_EXPORT_LIST = 'user.user.import_export_list';

    //MRP(国内)-》MRP V3-》修正后销售明细统计表
    const MRP_REPORT_ORDERS_MOD_BEF_DETAIL_V3_LIST = 'mrp.mrp_report_orders_mod_bef_detail_v3.list';
    const MRP_REPORT_ORDERS_MOD_BEF_DETAIL_V3_EXPORT = 'mrp.mrp_report_orders_mod_bef_detail_v3.export';

    //MRP(国内)-》MRP V3-》修正前销售明细统计表
    const MRP_REPORT_ORIG_SALESDATA_MOD_DETAIL_V3_LIST = 'mrp.mrp_report_orig_salesdata_mod_detail_v3.list';
    const MRP_REPORT_ORIG_SALESDATA_MOD_DETAIL_V3_EXPORT = 'mrp.mrp_report_orig_salesdata_mod_detail_v3.export';

    //MRP(国内)-》MRP V3-》销量源数据（修正后）
    const MRP_REPORT_ORIG_SALESDATA_MOD_V3_LIST = 'mrp.mrp_report_orig_salesdata_mod_v3.list';
    const MRP_REPORT_ORIG_SALESDATA_MOD_V3_EXPORT = 'mrp.mrp_report_orig_salesdata_mod_v3.export';

    //MRP(国内)-》MRP V3-》销量源数据
    const MRP_REPORT_ORIG_SALESDATA_V3_LIST = 'mrp.mrp_report_orig_salesdata_v3.list';
    const MRP_REPORT_ORIG_SALESDATA_V3_EXPORT = 'mrp.mrp_report_orig_salesdata_v3.export';

    //MRP(国内)-》MRP V3-》SKU销量统计（修正后）列表
    const ROUTE_MRP_REPORT_ORDERS_MOD_AFT_V3_LIST = 'mrp.mrp_report_orders_mod_aft_v3.list';
    //MRP(国内)-》MRP V3-》SKU销量统计（修正后）导出
    const ROUTE_MRP_REPORT_ORDERS_MOD_AFT_V3_EXPORT = 'mrp.mrp_report_orders_mod_aft_v3.export';

    //MRP(国内)-》MRP V3-》SKU日销量统计（修正前）列表
    const ROUTE_MRP_SKU_ORDERS_MOD_BEF_V3_LIST = 'mrp.mrp_sku_orders_mod_bef_v3.list';
    //MRP(国内)-》MRP V3-》SKU日销量统计（修正前）导出
    const ROUTE_MRP_SKU_ORDERS_MOD_BEF_V3_EXPORT = 'mrp.mrp_sku_orders_mod_bef_v3.export';

    //MRP(国内)-》MRP V3-》SKU库存统计 列表
    const ROUTE_MRP_REPORT_STOCK_COUNT_V3_LIST = 'mrp.mrp_report_stock_count_v3.list';
    //MRP(国内)-》MRP V3-》SKU库存统计 导出
    const ROUTE_MRP_REPORT_STOCK_COUNT_V3_EXPORT = 'mrp.mrp_report_stock_count_v3.export';

    //MRP(国内)-》总缺货订单明细 列表
    const ROUTE_MRP_REPORT_OOS_ORDERS_DETAIL_TOTAL_LIST = 'mrp.mrp_report_oos_orders_detail_total.list';
    //MRP(国内)-》总缺货订单明细 导出
    const ROUTE_MRP_REPORT_OOS_ORDERS_DETAIL_TOTAL_EXPORT = 'mrp.mrp_report_oos_orders_detail_total.export';


    //MRP(国内)-》sku日均销量统计报表
    const MRP_REPORT_DAY_SALES_COUNT_LIST = 'mrp.mrp_report_day_sales_count.list';
    //MRP(国内)-》sku日均销量统计报表 导出
    const MRP_REPORT_DAY_SALES_COUNT_EXPORT = 'mrp.mrp_report_day_sales_count.export';

    //MRP(国内)-》日缺货率统计报表 列表
    const MRP_REPORT_OOS_ORDERS_D_LIST = 'mrp.mrp_report_oos_orders_d.list';
    //MRP(国内)-》日缺货率统计报表 导出
    const MRP_REPORT_OOS_ORDERS_D_EXPORT = 'mrp.mrp_report_oos_orders_d.export';

    //MRP(国内)-》周缺货率统计报表 列表
    const MRP_REPORT_OOS_ORDERS_W_LIST = 'mrp.mrp_report_oos_orders_w.list';
    //MRP(国内)-》周缺货率统计报表 导出
    const MRP_REPORT_OOS_ORDERS_W_EXPORT = 'mrp.mrp_report_oos_orders_w.export';

    //MRP(国内)-》月缺货率统计报表 列表
    const MRP_REPORT_OOS_ORDERS_M_LIST = 'mrp.mrp_report_oos_orders_m.list';
    //MRP(国内)-》月缺货率统计报表 导出
    const MRP_REPORT_OOS_ORDERS_M_EXPORT = 'mrp.mrp_report_oos_orders_m.export';

    //MRP(国内)-》平台+SKU销量统计 列表
    const MRP_REPORT_SALES_COUNT_PLATFORM_LIST = 'mrp.mrp_report_sales_count_platform.list';
    //MRP(国内)-》平台+SKU销量统计 导出
    const MRP_REPORT_SALES_COUNT_PLATFORM_EXPORT = 'mrp.mrp_report_sales_count_platform.export';


    //MRP(国内)-》平台+SKU销量统计 列表
    const MRP_REPORT_OOS_ORDERS_D_V2_LIST = 'mrp.mrp_report_oos_orders_d_v2.list';
    //MRP(国内)-》平台+SKU销量统计 导出
    const MRP_REPORT_OOS_ORDERS_D_V2_EXPORT = 'mrp.mrp_report_oos_orders_d_v2.export';

    //MRP(国内)-》平台+SKU销量统计 列表
    const MRP_REPORT_OOS_ORDERS_D_ALL_V2_LIST = 'mrp.mrp_report_oos_orders_d_all_v2.list';
    //MRP(国内)-》平台+SKU销量统计 导出
    const MRP_REPORT_OOS_ORDERS_D_ALL_V2_EXPORT = 'mrp.mrp_report_oos_orders_d_all_v2.export';

    //MRP(国内)-》平台+SKU销量统计 列表
    const MRP_REPORT_OOS_ORDERS_DETAIL_DAILY_LIST = 'mrp.mrp_report_oos_orders_detail_daily.list';
    //MRP(国内)-》平台+SKU销量统计 导出
    const MRP_REPORT_OOS_ORDERS_DETAIL_DAILY_EXPORT = 'mrp.mrp_report_oos_orders_detail_daily.export';

    //MRP(国内)-》平台+SKU销量统计 列表
    const MRP_REPORT_OOS_ORDERS_DETAIL_TOTAL_LIST = 'mrp.mrp_report_oos_orders_detail_total.list';
    //MRP(国内)-》平台+SKU销量统计 导出
    const MRP_REPORT_OOS_ORDERS_DETAIL_TOTAL_EXPORT = 'mrp.mrp_report_oos_orders_detail_total.export';

    //MRP(国内)-》平台+SKU销量统计 列表
    const MRP_REPORT_SALES_COUNT_PLATFORM_ALL_LIST = 'mrp.mrp_report_sales_count_platform_all.list';
    //MRP(国内)-》平台+SKU销量统计 导出
    const MRP_REPORT_SALES_COUNT_PLATFORM_ALL_EXPORT = 'mrp.mrp_report_sales_count_platform_all.export';


    //MRP(国内)-》MRP V3-》销量明细统计表(剔除海狮，BB业务线) 列表
    const MRP_REPORT_ORIG_SALESDATA_DETAIL_V3_NEW_LIST = 'mrp.mrp_report_orig_salesdata_detail_v3_new.list';
    //MRP(国内)-》MRP V3-》销量明细统计表(剔除海狮，BB业务线) 导出
    const MRP_REPORT_ORIG_SALESDATA_DETAIL_V3_NEW_EXPORT = 'mrp.mrp_report_orig_salesdata_detail_v3_new.export';

    //MRP(国内)-》MRP V3-》销量-SKU明细 列表
    const MRP_REPORT_SALES_COUNT_SKU_DETAIL_LIST = 'mrp.mrp_report_sales_count_sku_detail.list';
    //MRP(国内)-》MRP V3-》销量-SKU明细 导出
    const MRP_REPORT_SALES_COUNT_SKU_DETAIL_EXPORT = 'mrp.mrp_report_sales_count_sku_detail.export';

    //MRP(国内)-》MRP V3-》销量明细统计表 列表
    const MRP_REPORT_ORIG_SALESDATA_DETAIL_V3_LIST = 'mrp.mrp_report_orig_salesdata_detail_v3.list';
    //MRP(国内)-》MRP V3-》销量明细统计表 导出
    const MRP_REPORT_ORIG_SALESDATA_DETAIL_V3_EXPORT = 'mrp.mrp_report_orig_salesdata_detail_v3.export';

    //MRP(国内)-》MRP V3-》计算SKU自动补货 列表
    const MRP_RESULT_PLAN_V3_LIST = 'mrp.mrp_result_plan_v3.list';
    //MRP(国内)-》MRP V3-》计算SKU自动补货 导出
    const MRP_RESULT_PLAN_V3_EXPORT = 'mrp.mrp_result_plan_v3.export';


    //MRP(国内)-》MRP V3-》销量源数据
    const MRP_MRP_REPORT_ORIG_SALESDATA_SF_LIST = 'mrp.mrp_report_orig_salesdata_sf.list';
    const MRP_MRP_REPORT_ORIG_SALESDATA_SF_EXPORT = 'mrp.mrp_report_orig_salesdata_sf.export';

    //MRP(国内)-》MRP SF-》销量统计（HS）
    const MRP_MRP_REPORT_ORDERS_SF_LIST = 'mrp.mrp_report_orders_sf.list';
    const MRP_MRP_REPORT_ORDERS_SF_EXPORT = 'mrp.mrp_report_orders_sf.export';

    //MRP(国内)-》MRP SF-》自动补货建议（HS）
    const MRP_MRP_RESULT_PLAN_SF_LIST = 'mrp.mrp_result_plan_sf.list';
    const MRP_MRP_RESULT_PLAN_SF_EXPORT = 'mrp.mrp_result_plan_sf.export';

    //MRP(国内)-》历史每日缺货占比统计表
    const MRP_REPORT_OOS_ORDERS_D_HIS_V2_LIST = 'mrp.mrp_report_oos_orders_d_his_v2.list';
    const MRP_REPORT_OOS_ORDERS_D_HIS_V2_EXPORT = 'mrp.mrp_report_oos_orders_d_his_v2.export';
    //MRP(国内)-》MRP V3-》销量-SKU统计
    const MRP_REPORT_SALES_COUNT_SKU_LIST = 'mrp.mrp_report_sales_count_sku.list';
    const MRP_REPORT_SALES_COUNT_SKU_EXPORT = 'mrp.mrp_report_sales_count_sku.export';


    //MRP(国内)-》MRP SF-》库存统计（HS）
    const MRP_MRP_REPORT_STOCK_COUNT_SF_LIST = 'mrp.mrp_report_stock_count_sf.list';
    const MRP_MRP_REPORT_STOCK_COUNT_SF_EXPORT = 'mrp.mrp_report_stock_count_sf.export';

    //MRP(国内)-》MRP SF-》shopify平台sku日销量统计报表
    const MRP_MRP_REPORT_DAY_SALES_COUNT_SF_LIST = 'mrp.mrp_report_day_sales_count_sf.list';
    const MRP_MRP_REPORT_DAY_SALES_COUNT_SF_EXPORT = 'mrp.mrp_report_day_sales_count_sf.export';

    //MRP(国内)-》MRP SF-》备货关系表(HS)
    const MRP_MRP_BASE_SKU_CORE_SF_LIST = 'mrp.mrp_base_sku_core_sf.list';
    const MRP_MRP_BASE_SKU_CORE_SF_EXPORT = 'mrp.mrp_base_sku_core_sf.export';
    const MRP_MRP_BASE_SKU_CORE_SF_IMPORT = 'mrp.mrp_base_sku_core_sf.import';

    //MRP(国内)-》MRP V3-》sku对应关系列表V3
    const MRP_MRP_BASE_SKU_CORE_V3_LIST = 'mrp.mrp_base_sku_core_v3.list';
    const MRP_MRP_BASE_SKU_CORE_V3_EXPORT = 'mrp.mrp_base_sku_core_v3.export';
    const MRP_MRP_BASE_SKU_CORE_V3_IMPORT = 'mrp.mrp_base_sku_core_v3.import';
    //模块字典
    const ROUTE_COMMON_MODULE_DICT = 'common.dict.index';


    //模块字典
    const ROUTE_SERVER_USER_INFO = 'serve.user.info';
    //mrp模块
    const  TEST = 'mrp.report.test';


    /*==*==*==*==*==*==*==*== dwj ==*==*==*==*==*==*==*==*/
    // 接收WMS占用库存
    const ROUTE_MRP_WMS_STOCK_SKU_USE_QTY = 'mrp.wmsStock.updateSkuUseQty';
    // 接收WMS离位库存
    const ROUTE_MRP_WMS_STOCK_SKU_LEAVE_QTY = 'mrp.wmsStock.updateSkuLeaveQty';
    // 接收WMS库存
    const ROUTE_MRP_WMS_ACTUAL_STOCK_SKU_QTY = 'mrp.wmsStock.updateSkuActualStockQty';

    // PR批量上传
    const PR_BATCH_UPLOAD_IMPORT = 'prupload.poBatchUpload.import';
    const PR_BATCH_UPLOAD_LIST = 'prupload.poBatchUpload.list';
    const PR_BATCH_UPLOAD_EXPORT_LIST = 'prupload.poBatchUpload.exportList';
    const PR_BATCH_UPLOAD_SAVE = 'prupload.poBatchUpload.save';
    const PR_BATCH_UPLOAD_IMPORT_SAVE = 'prupload.poBatchUpload.importSave';
    const PR_BATCH_UPLOAD_CANCEL = 'prupload.poBatchUpload.cancel';
    const PR_BATCH_UPLOAD_UPDATE_ERROR = 'prupload.poBatchUpload.updateError';

    // SKU进度跟进
    const SKU_FOLLOW_LIST = 'prupload.skuFollow.list';
    const SKU_FOLLOW_EXPORT = 'prupload.skuFollow.export';

    // 接收PMS PR单相关数据
    const PR_PMS_CANCEL_PR = 'prupload.pms.cancelPr';
    const PR_PMS_RECEIVE_PR_DATA = 'prupload.pms.receivePrData';

    //撤销在途功能
    const CANCELPO_GET_ORDER_LISTS = 'mrp.cancelpo.getOrderLists';
    const CANCELPO_EXPORT_ORDER_LISTS = 'mrp.cancelpo.exportOrderLists';
    const CANCELPO_OPERATE_ORDER_LISTS = 'mrp.cancelpo.operateOrderLists';
    const CANCELPO_GET_TOTAL_RESULT_LISTS = 'mrp.cancelpo.getTotalResultLists';
    const CANCELPO_EXPORT_TOTAL_RESULT_LISTS = 'mrp.cancelpo.exportTotalResultLists';
    const CANCELPO_GET_DETAIL_RESULT_LISTS = 'mrp.cancelpo.getDetaillResultLists';
    const CANCELPO_EXPORT_DETAIL_RESULT_LISTS = 'mrp.cancelpo.exportDetaillResultLists';
    const CANCELPO_UPLOAD_DETAIL_RESULT_LISTS = 'mrp.cancelpo.uploadDetaillResultLists';


}
