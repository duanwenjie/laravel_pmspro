<?php
/**
 * Description
 * User: dwj
 * Date: 2021/5/8
 * Time: 11:16 上午
 */

namespace App\Http\ConfigBase;

use App\Models\BaseColumnList;
use App\Tools\CacheSet;
use Illuminate\Support\Facades\Cache;

class ConfigBase
{
    private const ORDER_STATUS_AMP = 'order_status'; // OMS包裹状态

    private const WMS_WAREHOUSE_AMP = 'warehouse_id'; // WMS仓库

    private const STOCK_WAY_AMP = 'stock_way'; // 备货方式

    private const SALES_STATUS = 'sales_status'; // 销售状态

    private const CONFIRM_STATUS = 'confirm_status'; // 确认状态

    private const SKU_SALES_STATUS = 'sku_sales_status'; // SKU基础资料销售状态 pms_po_detail_status

    private const PMS_PO_DETAIL_STATUS = 'pms_po_detail_status'; // PMS采购单明细状态

    private const PMS_PR_LEAVE_USER = 'pr_leave_user'; // PMS 已离职或转岗计划人员

    //减少缓存读取次数
    static $warehouseMap;
    static $orderStatusMap;
    static $stockWayMap;
    static $salesStatusMap;
    static $confirmStatusMap;
    static $skuSalesStatusMap;
    static $pmsPoDetailStatusMap;
    static $prLeaveUserMap;

    /**
     * 通过columnName获取通用配置的columnValue值
     * @param $columnName  : 字段名
     * @return array
     * @author dwj
     */
    protected static function getMapByColumnName($columnName)
    {
        $key = CacheSet::CONFIG_BASE_GET.'_'.$columnName;
        $data = Cache::get($key);
        if (empty($data)) {
            $data = BaseColumnList::query()->where('column_name', $columnName)
                ->select(['column_key', 'column_value'])
                ->pluck('column_value', 'column_key')
                ->toArray();
            Cache::add($key, $data, CacheSet::TTL_MAP[CacheSet::CONFIG_BASE_GET]);
        }
        return $data;
    }

    /**
     * 获取OMS包裹状态
     * @return array
     * @author dwj
     */
    public static function getOrderStatusMap()
    {
        if(empty(self::$orderStatusMap)){
            self::$orderStatusMap = self::getMapByColumnName(self::ORDER_STATUS_AMP);
        }
        return self::$orderStatusMap;
    }

    /**
     * 获取WMS仓库
     * @return array
     * @author dwj
     */
    public static function getWarehouseMap()
    {
        if(empty(self::$warehouseMap)){
            self::$warehouseMap = self::getMapByColumnName(self::WMS_WAREHOUSE_AMP);
        }
        return self::$warehouseMap;
    }

    /**
     * 获取备货方式名称
     * @return array
     */
    public static function getStockWayMap()
    {
        if(empty(self::$stockWayMap)){
            self::$stockWayMap = self::getMapByColumnName(self::STOCK_WAY_AMP);
        }
        return self::$stockWayMap;
    }

    /**
     * 获取销售状态名称
     * @return array
     */
    public static function getSalesStatusMap()
    {
        if(empty(self::$salesStatusMap)){
            self::$salesStatusMap = self::getMapByColumnName(self::SALES_STATUS);
        }
        return self::$salesStatusMap;
    }

    /**
     * 获取确认状态名称
     * @return array
     */
    public static function getConfirmStatusMap()
    {
        if(empty(self::$confirmStatusMap)){
            self::$confirmStatusMap = self::getMapByColumnName(self::CONFIRM_STATUS);
        }
        return self::$confirmStatusMap;
    }

    /**
     * 获取SKU基础资料SKU销售状态
     * @return array
     * @author dwj
     */
    public static function getSkuSalesStatusMap()
    {
        if(empty(self::$skuSalesStatusMap)){
            self::$skuSalesStatusMap = self::getMapByColumnName(self::SKU_SALES_STATUS);
        }
        return self::$skuSalesStatusMap;
    }

    /**
     * 获取PMS采购单明细SKU订单状态
     * @return array
     * @author dwj
     */
    public static function getPmsPoDetailStatusMap()
    {
        if(empty(self::$pmsPoDetailStatusMap)){
            self::$pmsPoDetailStatusMap = self::getMapByColumnName(self::PMS_PO_DETAIL_STATUS);
        }
        return self::$pmsPoDetailStatusMap;
    }

    /**
     * 获取PMS 已离职或转岗计划人员
     * @return array
     * @author dwj
     */
    public static function getPrLeaveUserMap()
    {
        if(empty(self::$prLeaveUserMap)){
            self::$prLeaveUserMap = self::getMapByColumnName(self::PMS_PR_LEAVE_USER);
        }
        return self::$prLeaveUserMap;
    }
}
