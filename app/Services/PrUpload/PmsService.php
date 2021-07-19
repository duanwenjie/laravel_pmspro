<?php
/**
 * Description
 * User: dwj
 * Date: 2021/5/27
 * Time: 5:34 下午
 */

namespace App\Services\PrUpload;

use App\Exceptions\InvalidRequestException;
use App\Http\ConfigBase\ConfigBase;
use App\Models\PrUpload\PrUploadSkusList;
use App\Tools\Client\PmsClient;
use Carbon\Carbon;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class PmsService
{
    /**
     * 进销存取消PR单，更新PMS PR单状态为已取消
     * @param  array  $prIds  int PR单号(主键ID)
     * @return int
     * @author dwj
     */
    public static function cancelPr(array $prIds)
    {
        return DB::connection('pms')->table('purchaseplan')
            ->whereIn('pr_id', $prIds)
            ->update(['state' => 4]); // PMS PR单状态为撤销
    }

    /**
     * 获取PMS采购单信息
     * @param  array  $prIds
     * @return Collection
     * @author dwj
     */
    public static function getPoInfo(array $prIds)
    {
        return DB::connection('pms')->table('purchaseplan as A')
            ->leftjoin('purchaseplan_order as B', 'A.id', '=', 'B.purchaseplan_id')
            ->leftjoin('purchaseorder_detail as C', 'B.purchaseorder_detail_id', '=', 'C.id')
            ->leftjoin('purchaseorder as D', 'B.purchaseorder_id', '=', 'D.id')
            ->select([
                'A.pr_id',
                'D.id as po',
                'D.create_time as purchase_date',
                'D.merchandiser as purchase',
                'C.quantity',
                'C.last_ware_date',
                'C.delivery_date',
                'C.ware_quantity',
                'C.state',
            ])
            ->whereIn('A.pr_id', $prIds)
            ->get()
            ->keyBy('pr_id');
    }

    /**
     * PMS系统撤销PR单
     * @param $request
     * @return array|string[]
     * @author dwj
     */
    public static function cancelPrByPms($request)
    {
        $quantity = $request['quantity'] ?? 0;
        $prId = $request['pr_id'];
        if ($quantity == 0) {
            return ['code' => '000001', 'msg' => '释放数量为空'];
        }

        $users = ConfigBase::getPrLeaveUserMap()['users'] ?? [];
        !empty($users) && $users = explode(',', $users);
        $builder = PrUploadSkusList::query()->where('id', $prId);

        // 已离职或已转岗的无需释放
        if (!empty($users)) {
            $builder->whereNotIn('user_id', $users);
        }

        $prInfo = $builder->select()->first();

        if (empty($prInfo->id)) {
            throw new InvalidRequestException('pr不存在或无需释放');
        }

        $po = $prInfo->po ?? 0;
        $sku = $prInfo->sku ?? '';
        if ($po > 0) {
            // PMS撤销总量计算明细表 有采购单 PR单就只撤销不释放
            $cancelPoInfo = DB::connection('pms')
                ->table('cancel_po_bt_detail')
                ->where('po_id', $po)
                ->where('sku', $sku)
                ->where('is_show', 1) // PMS是否显示 1:显示
                ->select(['po_id'])
                ->get();

            // PMS采购单创建时间超过60天 PR单之撤销不释放
            $oldDay = Carbon::now()->subDays(60)->format('Y-m-d');
            $overTimeInfo = DB::connection('pms')
                ->table('purchaseplan as l')
                ->join('purchaseplan_order as po', 'l.id', '=', 'po.purchaseplan_id')
                ->join('purchaseorder as o', 'po.purchaseorder_id', '=', 'o.id')
                ->where('l.pr_id', $prId)
                ->where('o.create_time', '<', $oldDay)
                ->select(['po.purchaseorder_id'])
                ->get();

            // 只撤销
            if (!empty($cancelPoInfo->po_id) || !empty($overTimeInfo->purchaseorder_id)) {
                PrUploadSkusList::query()->where('id', $prId)->update(['status' => PrUploadSkusList::cancel]);
            } else {
                // 撤销并释放
                PrUploadSkusList::query()->where('id', $prId)->update(['status' => PrUploadSkusList::cancel]);
                PoBatchUploadService::releasePr([$prId]);
            }
        } else {
            // 撤销并释放
            PrUploadSkusList::query()->where('id', $prId)->update(['status' => PrUploadSkusList::cancel]);
            PoBatchUploadService::releasePr([$prId]);
        }

        self::pushData($prId); // 推送数据到PMS
        return ['code' => '000001', 'msg' => '操作成功'];
    }

    /**
     * 推送已撤销PR单数据到PMS
     * @param $prId
     * @throws InvalidRequestException
     * @author dwj
     */
    public static function pushData($prId)
    {
        $url = 'api/purchaseplan/receiveData'; // PMS 接收计划单数据URL
        $data = [];
        PrUploadSkusList::query()
            ->where('status', PrUploadSkusList::cancel)
            ->whereIn('check_status', [PrUploadSkusList::pass, PrUploadSkusList::error])
            ->where('id', $prId)
            ->select([
                'id as pr_id',
                'sku',
                'warehouseid',
                'user_id',
                'quantity',
                'created_at as upload_time',
                'require_date'
            ])
            ->each(function ($item) use (&$data) {
                // 需求日期、预计交货日期 在PR单上传时间基础上默认+3天
                $uploadTime = $item->upload_time;
                $date = Carbon::parse($uploadTime)->addDays(3)->format('Y-m-d');
                $userInfo = getUserInfo($item->user_id);
                $data[] = [
                    'pr_id'         => $item->pr_id,
                    'sku'           => $item->sku,
                    'warehouse_id'  => ($item->warehouseid == 106) ? 6 : 3,
                    'planner'       => $userInfo['username'] ?? '', // 计划员账号
                    'quantity'      => $item->quantity,
                    'upload_time'   => $uploadTime,
                    'require_date'  => $date,
                    'delivery_date' => $date,
                ];
            });

        if (empty($data)) {
            throw new InvalidRequestException('操作失败，原因【推送到PMS系统PR单数据为空，请核对！】');
        }

        $curlRes = PmsClient::call($url, $data);
        if ($curlRes['state'] != '000001') {
            throw new InvalidRequestException("操作失败，原因【PMS系统返回失败：{$curlRes['msg']}，请核对！】");
        }
    }


    /**
     * PMS创建采购单成功，更新PR单的状态和采购单明细状态
     * @param $request
     * @author dwj
     */
    public static function receivePrDataByPms($request)
    {
        $list = $request['list'];
        $update = [];
        foreach ($list as $v) {
            if ($v['pr_id'] < PrUploadSkusList::pmsProPrIdStart){
                continue; // 非新系统PR单跳过
            }
            $update[] = [
                'id'                   => $v['pr_id'],
                'po'                   => $v['po'],
                'status'               => PrUploadSkusList::handle,
                'pms_po_detail_status' => 2, // PMS采购单明细SKU状态 2：待采购
            ];
        }
        !empty($update) && PrUploadSkusList::query()->upsert($update, ['id']);
        return ['code' => '000001', 'msg' => '操作成功'];
    }

    /**
     * 同步PMS PR单状态到进销存PR单列表
     * 规则：同步近三天的PR单
     * @author dwj
     */
    public static function syncPmsPrStatus()
    {
        $times = Carbon::now()->format('Y-m-d H:i:s');
        Log::info("同步PMS PR单状态到进销存PR单列表 || 开始");
        $i = 0;
        $date = Carbon::now()->subDays(3)->format('Y-m-d');

        DB::connection('pms')->table('purchaseplan as p')
            ->leftJoin('purchaseplan_order as po', 'p.id', '=', 'po.purchaseplan_id')
            ->leftJoin('purchaseorder_detail as pd', 'po.purchaseorder_detail_id', '=', 'pd.id')
            ->where('pd.last_update_time', '>', $date)
            ->where('p.pr_id','>=',PrUploadSkusList::pmsProPrIdStart) // 只同步新系统的PR单
            ->select(
                [
                    'p.pr_id as id',
                    'po.purchaseorder_id as po',
                    'pd.state as pms_po_detail_status',
                ]
            )
            ->chunkById(1000, function ($items) use (&$i) {
                $i += count($items);
                $prIds = array_column($items->toArray(),'id');
                $prInfos = PrUploadSkusList::query()->whereIn('id',$prIds)->pluck('status','id');
                $update = [];
                foreach ($items as $item) {
                    $prStatus = $prInfos[$item->id] ?? '';
                    if ($prStatus == PrUploadSkusList::cancel){
                        continue; // PR单已撤销的不再更新状态
                    }
                    // 采购单明细SKU状态映射PR单状态
                    $status = PrUploadSkusList::pmsPoDetailStatusShine[$item->pms_po_detail_status] ?? '';
                    if (empty($status)){
                        continue; // 排除脏数据
                    }
                    $update[] = [
                        'id'                   => $item->id,
                        'status'               => $status,
                        'po'                   => $item->po,
                        'pms_po_detail_status' => $item->pms_po_detail_status,
                    ];
                }
                !empty($update) && PrUploadSkusList::query()->upsert($update, ['id']);
                //Log::info("执行完{$i}条");
            },'p.pr_id','id');

        $timeEnd = Carbon::now()->format('Y-m-d H:i:s');
        $timeDiff = Carbon::parse($timeEnd)->diffInMinutes($times);
        Log::info("结束 || 同步PMS PR单状态到进销存PR单列表花费时间：{$timeDiff}分，执行完{$i}条");
    }

    /**
     * 获取PMS SKU的入库记录
     * @param array $skus
     * @return array
     * @author dwj
     */
    public static function getSkuWareRecord(array $skus)
    {
        return DB::connection('pms')->table('sku_ware_record')
            ->whereIn('sku',$skus)
            ->pluck('record','sku')
            ->toArray();
    }
}
