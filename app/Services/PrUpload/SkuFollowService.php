<?php
/**
 * Description
 * User: dwj
 * Date: 2021/5/28
 * Time: 3:01 下午
 */

namespace App\Services\PrUpload;

use App\Exceptions\InvalidRequestException;
use App\Exports\PrUpload\SkuFollowExport;
use App\ModelFilters\PrUpload\SkuFollowFilter;
use App\Models\Mrp\MrpBaseSkuCore;
use App\Models\PrUpload\PrUploadSkuFollowList;
use App\Models\PrUpload\PrUploadSkusList;
use App\Services\Common\SkuBaseService;
use App\Tools\Client\YksFileSystem;
use App\Tools\Formater;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Maatwebsite\Excel\Facades\Excel;

class SkuFollowService
{
    protected $downloadLimitRows = 300000; // 最大下载条数

    /**
     * SKU跟进列表状态字典
     * @return array
     * @author dwj
     */
    public function getSkuFollowDict()
    {
        return [
            'skuStatus' => Formater::formatDict(PrUploadSkusList::pmsDetailSkuStatusMap),
        ];
    }

    /**
     * 获取列表
     * @return mixed
     * @author dwj
     */
    public function getSkuFollowList()
    {
        return $this->builder()->paginate(request()->input('perPage'));
    }

    /**
     * 导出列表数据
     * @return mixed
     * @throws InvalidRequestException
     * @author dwj
     */
    public function exportSkuFollowList()
    {
        ini_set('memory_limit', '1024M');
        $builder = $this->builder();
        if ($builder->count() > $this->downloadLimitRows) {
            throw new InvalidRequestException("导出记录数超{$this->downloadLimitRows}条请筛选条件");
        }
        $fileName = date('YmdHis').'_'."SKU进度跟进数据.csv";
        Excel::store(new SkuFollowExport($builder), $fileName, 'export', \Maatwebsite\Excel\Excel::CSV);
        return YksFileSystem::upload($fileName);
    }

    /**
     * @return mixed
     * @author dwj
     */
    private function builder()
    {
        return PrUploadSkuFollowList::query()
            ->select()
            ->orderByDesc('id')
            ->filter(request()->input('data', []), SkuFollowFilter::class);
    }

    /**
     * 同步跟进表数据
     * @author dwj
     */
    public static function syncSkuFollowData()
    {
        $times = Carbon::now()->format('Y-m-d H:i:s');
        Log::info("同步SKU跟进表数据 || 开始");
        $i = 0;
        PrUploadSkusList::query()
            ->where('check_status', '<>', PrUploadSkusList::noPass)
            ->where('status', '<>', PrUploadSkusList::cancel)
            ->with('skuInfo')
            ->select()
            ->chunkById(1000, function ($items) use (&$i) {
                $i += count($items);
                $skus = $items->pluck('sku')->unique()->toArray();
                $purchaseInfos = SkuBaseService::getSkuPurchserBySkus($skus);
                $planerInfos = SkuBaseService::getSkuPlanerBySkus($skus);
                $prIds = $items->pluck('id')->toArray();
                $poInfos = PmsService::getPoInfo($prIds);
                $skuScoreInfo = MrpBaseSkuCore::query()
                    ->where('type', MrpBaseSkuCore::TYPE_V3)
                    ->whereIn('sku', $skus)
                    ->select(['sku', 'supply_cycle'])
                    ->pluck('supply_cycle', 'sku');

                $update = [];
                foreach ($items as $item) {
                    $sku = $item->sku;
                    $prId = $item->id;
                    $pdQuantity = $poInfos[$prId]->quantity ?? 0; // 采购单明细SKU数量
                    $prQuantity = $item->quantity; // 计划单数量
                    $supplyCycle = $skuScoreInfo[$sku] ?? 0; // 备货交期
                    $supplyDate = Carbon::parse($item->created_at)->addDays($supplyCycle)->format('Y-m-d'); // (PR+供货周期)日期
                    $skuLastWareDate = $poInfos[$prId]->last_ware_date ?? ''; // 采购单明细SKU最近入库时间
                    $skuDeliveryDate = $poInfos[$prId]->delivery_date ?? ''; // 采购单明细SKU预计交货时间
                    // 清除脏数据
                    if ($skuLastWareDate == '1970-01-01 00:00:00') {
                        $skuLastWareDate = '';
                    }
                    if (in_array($skuDeliveryDate, ['0000-00-00', '1970-01-01'])) {
                        $skuDeliveryDate = '';
                    }
                    $delayDays = 0; // 入库延迟天数
                    if (!empty($skuDeliveryDate)) {
                        if (!empty($skuLastWareDate)) {
                            $skuLastWareDate = Carbon::parse($skuLastWareDate)->format('Y-m-d');
                            $delayDays = Carbon::parse($skuDeliveryDate)->diffInDays($skuLastWareDate, false);
                        } else {
                            $now = Carbon::now()->format('Y-m-d');
                            $delayDays = Carbon::parse($skuDeliveryDate)->diffInDays($now, false);
                        }
                    }
                    $warehouseQuantity = $poInfos[$prId]->warehouse_quantity ?? 0; // 入库数量
                    $warehouseDiffQuantity = $warehouseQuantity - $pdQuantity; // 入库量差异

                    $pmsPoDetailStatus = $poInfos[$prId]->state ?? 0; // 采购单明细SKU订单状态
                    $prStatus = PrUploadSkusList::pmsPoDetailStatusShine[$pmsPoDetailStatus] ?? $item->status; // PR单状态
                    $temp = [
                        'pr_id'                   => $prId,
                        'pr_date'                 => $item->created_at,
                        'pr_status'               => $prStatus,
                        'sku'                     => $sku,
                        'sku_name'                => $item->skuInfo['cn_name'] ?? '',
                        'sku_sales_status'        => $item->skuInfo['sales_status'] ?? '',
                        'pr_quantity'             => $prQuantity,
                        'pr_require_date'         => $item->require_date,
                        'planner'                 => $planerInfos[$sku]->nick ?? '',
                        'purchase'                => $purchaseInfos[$sku]->nick ?? '',
                        'po'                      => $poInfos[$prId]->po ?? 0,
                        'purchase_date'           => $poInfos[$prId]->purchase_date ?? 0,
                        'plan_quantity'           => $item->quantity,
                        'quantity'                => $pdQuantity,
                        'diff_quantity'           => ($pdQuantity > 0) ? ($pdQuantity - $prQuantity) : 0,
                        'supply_date'             => $supplyDate,
                        'arrive_date'             => $skuDeliveryDate,
                        'warehouse_date'          => $skuLastWareDate,
                        'delay_days'              => $delayDays,
                        'warehouse_quantity'      => $warehouseQuantity,
                        'warehouse_diff_quantity' => $warehouseDiffQuantity,
                        'pms_po_detail_status'    => $pmsPoDetailStatus,
                    ];
                    $update[] = $temp;
                }
                !empty($update) && PrUploadSkuFollowList::query()->insert($update);
                //Log::info("执行完{$i}条");
            });

        $timeEnd = Carbon::now()->format('Y-m-d H:i:s');
        $timeDiff = Carbon::parse($timeEnd)->diffInMinutes($times);
        Log::info("结束 || 同步SKU跟进表数据花费时间：{$timeDiff}分，执行完{$i}条");
    }


    /**
     * 更新最新的PR单采购数量和入库数量、采购差异数量、入库差异数量
     * 规则：第一步，先查询出所有跟进表按采购单+SKU维度分组后的采购单下SKU数大于1的数据，
     *      第二步，循环查询每一条采购单对应的PR单，将该条采购单的采购单数量和入库数量按照先进先出原则，分配采购单数量到每一条PR单的采购数量字段上
     *      第三步，采购数量分完后，PR单的采购数量重置为0，只要进行了分配，则PR单的采购差异数量，入库差异数量都重置为0
     * @author dwj
     */
    public static function updatePoPcs()
    {
        PrUploadSkuFollowList::query()
            ->where('po', '<>', 0)
            ->select([
                'id',
                'po',
                'sku',
                'quantity',
                'warehouse_quantity',
                DB::raw("count(1) as sku_num")
            ])
            ->groupBy('po', 'sku')
            ->having('sku_num', '>', 1) // SKU数大于1的数据
            ->chunkById(1000, function ($items) {
                $update = self::formatData($items);
                !empty($update) && PrUploadSkuFollowList::query()->upsert($update, ['id']); // 更新保存
            });
    }

    /**
     * 整理更新数据
     * @param $items
     * @return array
     * @author dwj
     */
    public static function formatData($items)
    {
        $update = [];
        foreach ($items as $item) {
            $po = $item->po;
            $sku = $item->sku;
            $quantity = $item->quantity; // 采购单的采购数量
            $warehouseQuantity = $item->warehouse_quantity; // 采购单的入库数量
            PrUploadSkuFollowList::query()
                ->where('po', $po)
                ->where('sku', $sku)
                ->select(['id', 'plan_quantity'])
                ->orderBy('id', 'asc')
                ->each(function ($value) use (&$update, &$quantity, &$warehouseQuantity) {
                    $planQuantity = $value->plan_quantity; // PR单的计划数量
                    $id = $value->id;
                    if ($quantity > 0) {
                        $update[] = [
                            'id'                      => $id,
                            'quantity'                => ($quantity > $planQuantity) ? $planQuantity : $quantity,
                            'diff_quantity'           => 0, // PO差异数量重置为0
                            'warehouse_quantity'      => ($warehouseQuantity > $planQuantity) ? $planQuantity : $warehouseQuantity,
                            'warehouse_diff_quantity' => 0, // 入库数量重置为0
                        ];
                        $quantity -= $planQuantity; // 分配完递减
                        $warehouseQuantity -= $planQuantity; // 分配完递减
                    } else {
                        $update[] = [
                            'id'                      => $id,
                            'quantity'                => 0, // 采购单数量分配完，重置为0
                            'diff_quantity'           => 0,
                            'warehouse_quantity'      => 0, // 采购单数量分配完，重置为0
                            'warehouse_diff_quantity' => 0,
                        ];
                    }
                });
        }
        return $update;
    }
}
