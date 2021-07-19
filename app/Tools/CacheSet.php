<?php

namespace App\Tools;

//存放缓存KEY
class CacheSet
{
    const COMMON_ACCOUNTS_KEY_VALUE = 'common_accounts_key_value';//存放有棵树账号键值'1'=>'KoCo-US'
    const SYSTEM_ERROR_NOTIFY = 'system_error_notify:';//系统异常发送消息的key
    const MRP_BASE_FINISH = 'mrp_base_finish';//mrp 基础数据抓取完成
    const MRP_V3_FINISH = 'mrp_v3_finish';//mrp 基础数据抓取完成
    const MRP_HS_FINISH = 'mrp_hs_finish';//mrp 基础数据抓取完成
    const MRP_FINISH = 'mrp_finish:';//mrp 基础数据抓取完成
    const MRP_OTHER_REPORT_FINISH = 'mrp_other_report_finish';//mrp 其他类型报表
    const CONFIG_BASE_GET = 'config_base_get'; // 基础配置获取key
    const MRP_FINISH_NOTIFY = 'mrp_finish_notify'; // 基础数据抓取完成后的通知

    //存储时长
    const TTL_MAP = [
        self::COMMON_ACCOUNTS_KEY_VALUE => 600,
        self::MRP_BASE_FINISH           => 3600, //一小时
        self::MRP_V3_FINISH             => 3600, //一小时
        self::MRP_HS_FINISH             => 3600, //一小时
        self::MRP_FINISH                => 3600 * 24 * 7, //保留近七天的mrp执行数据
        self::MRP_OTHER_REPORT_FINISH   => 3600,//一小时
        self::CONFIG_BASE_GET           => 3600,//保存1个小时
        self::MRP_FINISH_NOTIFY         => 3600,//保存1个小时
    ];
}
