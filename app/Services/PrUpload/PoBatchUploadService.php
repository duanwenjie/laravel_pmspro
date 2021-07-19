<?php
/**
 * Description
 * User: dwj
 * Date: 2021/5/20
 * Time: 2:11 下午
 */

namespace App\Services\PrUpload;

use App\Exceptions\InvalidRequestException;
use App\Imports\PrUpload\PoBatchUploadImport;
use App\ModelFilters\PrUpload\PoBatchUploadFilter;
use App\Models\PrUpload\PrUploadFilesList;
use App\Models\PrUpload\PrUploadSkusList;
use App\Models\User;
use App\Services\Common\SkuBaseService;
use App\Tools\Client\YksFileSystem;
use App\Tools\Formater;
use Carbon\Carbon;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use League\Csv\CannotInsertRecord;
use League\Csv\Writer;

class PoBatchUploadService
{
    // 上传文件名称
    private static $fileName = '';
    // 上传文件路径
    private static $filePath = '';
    // 最大下载条数
    protected $downloadLimitRows = 100000;

    // 导出表头
    public const titles = [
        'id'                => 'PR单号',
        'sku'               => 'SKU',
        'spu'               => 'SPU',
        'quantity'          => '下单数',
        'warehouse_name'    => "仓别",
        'status_name'       => '状态名称',
        'check_status_name' => '检测状态描述',
        'user_nick'         => '上传人',
        'purchaser'         => '采购员',
        'created_at'        => '上传时间',
        'require_date'      => '需求时间',
        'old_pr_id'         => '原PR单号',
        'po'                => '采购单号',
        'sku_ware_record'   => 'SKU入库标识',
        'remark'            => '备注',
        'check_result'      => '原因结果',
        'no_order_reason'   => '未下单原因',
    ];

    /**
     * PR单列表状态字典
     * @return array
     * @author dwj
     */
    public function getPoBatchUploadDict()
    {
        return [
            'checkStatus' => Formater::formatDict(PrUploadSkusList::checkStatusMap),
            'status'      => Formater::formatDict(PrUploadSkusList::statusMap),
        ];
    }

    /**
     * 处理上传数据
     * @param $data
     * @author dwj
     */
    public static function handleImportData($data)
    {
        $skus = array_column($data, 'sku');
        $planerInfo = SkuBaseService::getSkuPlanerBySkus($skus);
        $supplierInfo = SkuBaseService::getSkuSupplierBySkus($skus);
        $purchaserInfo = SkuBaseService::getSkuPurchserBySkus($skus);
        $userInfo = Auth::user();
        $userId = $userInfo['id'];
        $userName = $userInfo['username'];
        $nickName = $userInfo['nickname'];

        // 往上传文件表插入数据
        $prUploadFile = PrUploadFilesList::query()->create([
            'user_id'   => $userId,
            'file_name' => self::$fileName,
            'file_path' => self::$filePath,
        ]);

        $uploadFileId = $prUploadFile->id;
        $update = [];
        $errorMsg = '';
        $errorPrMsg = '';
        foreach ($data as $v) {
            $sku = $v['sku'];
            $error = '';
            $temp = [
                'upload_files_id' => $uploadFileId,
                'sku'             => $sku,
                'quantity'        => $v['quantity'],
                'warehouseid'     => ($v['warehouse_id'] == 6) ? 106 : 103,
                'user_id'         => $userId,
                'user_nick'       => $nickName,
                'remark'          => $v['remark'],
                'status'          => PrUploadSkusList::noHandle,
                'check_status'    => PrUploadSkusList::pass,
                'check_result'    => '验证通过',
                'require_date'    => $v['require_date'],
            ];
            if (!isset($planerInfo[$sku])) {
                $errorPrMsg .= "SKU:{$sku}对应的计划员没录入，请到sku基础资料库->采购员SKU信息管理添加,\n";
            }
            $planer = $planerInfo[$sku]->name ?? '';
            if (!empty($planer) && $userName != $planer) {
                $errorPrMsg .= "SKU:{$sku}的计划员不是当前的登陆用户，对应的计划员是{$planer},\n";
            }
            if (!isset($purchaserInfo[$sku])) {
                $temp['status'] = PrUploadSkusList::noHandle;
                $temp['check_status'] = PrUploadSkusList::error;
                $error .= "SKU:{$sku}对应的订货员没录入，请联系订货主管到sku基础资料库->采购员SKU信息管理添加,\n";
                $temp['check_result'] = $error;
            }
            $supplier = $supplierInfo[$sku] ?? [];
            if (empty($supplier)) {
                $temp['status'] = PrUploadSkusList::noHandle;
                $temp['check_status'] = PrUploadSkusList::error;
                $error .= "SKU:{$sku}对应的供应商没有，请到sku基础资料库->供应商SKU管理录入,\n";
                $temp['check_result'] = $error;
            }
            $errorMsg .= $error;
            $update[] = $temp;
        }

        if (!empty($errorPrMsg)){
            throw new InvalidRequestException("上传失败，原因：{$errorPrMsg}");
        }

        !empty($update) && PrUploadSkusList::query()->insert($update);
        if (!empty($errorMsg)) {
            PrUploadFilesList::query()->where('id', $uploadFileId)->update([
                'data_result'  => $errorMsg,
                'check_status' => PrUploadFilesList::noPass,
            ]);
        } else {
            PrUploadFilesList::query()->where('id', $uploadFileId)->update([
                'check_status' => PrUploadFilesList::pass,
                'data_result'  => '验证通过',
            ]);
        }
    }

    /**
     * 处理上传修改PR单数据
     * @param $data
     * @throws InvalidRequestException
     * @author dwj
     */
    public static function handleImportSaveData($data)
    {
        $ids = array_column($data, 'id');
        $user = Auth::user();
        $operatorId = $user->id ?? 0;
        $operatorName = $user->nickname ?? '';
        $prInfo = PrUploadSkusList::query()
            ->whereIn('id', $ids)
            ->select([
                'id',
                'status',
                'user_id',
                'quantity',
                'remark',
                'no_order_reason',
            ])
            ->get()
            ->keyBy('id')
            ->toArray();

        $tempAll = [];
        $errorMsg = [];
        $truePrIds = array_column($prInfo,'id');
        $errorPrIds = array_diff($ids,$truePrIds);
        if (!empty($errorPrIds)){
            $errorPrIdsStr = implode(',',$errorPrIds);
            $errorMsg[] = "PR单{$errorPrIdsStr}数据库不存在";
        }

        foreach ($prInfo as $id => $v) {
            $tempAll[$id] = [
                'id'              => $v['id'],
                'quantity'        => $v['quantity'],
                'remark'          => $v['remark'],
                'no_order_reason' => $v['no_order_reason'],
            ];
            if ($v['status'] != 0) {
                $errorMsg[] = "PR单{$id}的状态必须为待处理";
            }
            if ($v['user_id'] != $operatorId && $operatorName != 'admin') { // 超管可以操作保存和撤销
                $errorMsg[] = "PR单{$id}当前操作员为{$operatorName}不是PR单的上传人员";
            }
        }

        if (!empty($errorMsg)){
            $errorMsgStr = implode(',',$errorMsg);
            throw new InvalidRequestException("操作失败，原因：【{$errorMsgStr}，请核对！】");
        }

        foreach ($data as $v) {
            $temp = $tempAll[$v['id']];
            if (Arr::get($v, 'quantity')) {
                $temp['quantity'] = $v['quantity'];
            }
            if (Arr::get($v, 'remark')) {
                $temp['remark'] = $v['remark'];
            }
            if (Arr::get($v, 'no_order_reason')) {
                $temp['no_order_reason'] = $v['no_order_reason'];
            }
            $update[] = $temp;
        }
        PrUploadSkusList::query()->upsert($update, ['id']);
    }

    /**
     * 导入PR
     * @param  array  $requestParam
     * @author dwj
     */
    public function import($requestParam = [])
    {
        //$importExportRecord = $requestParam['import_export_record'];
        //self::$fileName = $requestParam['data']['fileName'] ?? '';
        //self::$filePath = $requestParam['data']['fileUrl'] ?? '';
        //
        //Storage::disk('export')->put(
        //    $importExportRecord->file_name,
        //    file_get_contents($importExportRecord->file_upload_url)
        //);
        //
        //$filePath = file_save_path($importExportRecord->file_name, 'export');
        //(new PoBatchUploadImport())->import($filePath);
        //$importExportRecord->update(
        //    [
        //        'status'       => UserImportExportRecord::STATUS_SUCCESS,
        //        'completed_at' => Carbon::now(),
        //        'result'       => '处理成功'
        //    ]
        //);
        //
        //Storage::disk('export')->delete($importExportRecord->file_name);


        // 改为同步
        $filePath = $requestParam;
        (new PoBatchUploadImport())->import($filePath);
    }

    /**
     * 获取列表
     * @return array
     * @author dwj
     */
    public function getList()
    {
        $list = $this->builder()->paginate(request()->input('perPage'));
        return self::formatData($list);
    }

    /**
     * @return mixed
     * @author dwj
     */
    private function builder()
    {
        return PrUploadSkusList::query()
            ->select()
            ->with('skuInfo')
            ->orderByDesc('id')
            ->filter(request()->input('data', []), PoBatchUploadFilter::class);
    }

    /**
     * 整理列表数据
     * @param $builder
     * @return array
     * @author dwj
     */
    public static function formatData($list)
    {
        $skus = $list->pluck('sku')->toArray();
        $purchaseInfo = SkuBaseService::getSkuPurchserBySkus($skus);
        foreach ($list as &$v) {
            $v['purchaser'] = $purchaseInfo[$v['sku']]->nick ?? '';
            $v['spu'] = $v['skuInfo']['spu'] ?? '';
            $v['skuName'] = $v['skuInfo']['cn_name'] ?? '';
        }
        return $list;
    }

    /**
     * 导出列表
     * @return mixed
     * @throws CannotInsertRecord
     * @throws InvalidRequestException
     * @author dwj
     */
    public function exportList()
    {
        ini_set('memory_limit', '2048M');
        $fileName = date('YmdHis').'_'."批量上传SKU数据.csv";
        $builder = $this->builder();
        if ($builder->count() > $this->downloadLimitRows) {
            throw new InvalidRequestException("导出记录数超{$this->downloadLimitRows}条请筛选条件");
        }
        $header = [
            '序号',
            'sku',
            'spu',
            '级别',
            '下单数',
            '仓库',
            '仓别',
            '采购',
            '跟单',
            '平台',
            '团队',
            '状态',
            '验证状态',
            '原因结果',
            '上传人',
            '备注',
            '上传时间',
            '异常原因',
            '原PR单号',
            '未下单原因',
            '采购单',
            '入库标识',
            '是否厂家柜'
        ];
        $filePath = file_save_path($fileName);
        $writer = Writer::createFromPath($filePath, 'w+');
        $writer->insertOne($header);
        $builder->chunkById(3000, function ($list) use (&$writer) {
            $temp = self::formatData($list);
            $data = [];
            foreach ($temp as $v) {
                $data[] = [
                    $v['id']."\t",
                    $v['sku'],
                    $v['spu'],
                    ' ', //级别
                    $v['quantity'],
                    ' ',//仓库
                    $v['warehouse_name'],
                    $v['purchaser'],
                    ' ',//跟单
                    ' ',//平台
                    ' ',//团队
                    $v['status_name'],
                    $v['check_status_name'],
                    $v['check_result'],
                    $v['user_nick'],
                    $v['remark'],
                    $v['created_at'],
                    ' ',//异常原因
                    $v['old_pr_id']."\t",
                    $v['no_order_reason'],
                    $v['po']."\t",
                    $v['sku_ware_record'],
                    ' '
                ];
            }
            $writer->insertAll($data);
        });
        return YksFileSystem::upload($fileName);
    }

    /**
     * 批量保存PR单/取消PR单
     * @param     $request
     * @param  int  $type  1: 保存 2：取消
     * @return string
     * @throws InvalidRequestException
     * @author dwj
     */
    public function saveOrCancel($request, int $type = 1)
    {
        $data = $request->input('data.list');
        $isRelease = $request->input('data.isRelease') ?? false; // 是否需要释放PR
        $user = Auth::user();
        $operatorId = $user->id ?? 0;
        $operatorName = $user->nickname ?? '';
        $ids = array_column($data, 'id');
        $prInfo = PrUploadSkusList::query()
            ->whereIn('id', $ids)
            ->select([
                'id',
                'status',
                'user_id',
                'user_nick',
                'check_status',
                'quantity',
                'require_date',
                'remark',
                'no_order_reason',
            ])
            ->get()
            ->keyBy('id')
            ->toArray();
        $update = [];
        if ($type == 1) {
            $tempAll = [];
            foreach ($prInfo as $id => $v) {
                $tempAll[$id] = [
                    'id'              => $v['id'],
                    'quantity'        => $v['quantity'],
                    'require_date'    => $v['require_date'],
                    'remark'          => $v['remark'],
                    'no_order_reason' => $v['no_order_reason'],
                ];
                if ($v['status'] != 0) {
                    throw new InvalidRequestException("操作失败，原因：【PR单{$id}的状态必须为待处理，请核对！】");
                }
                if ($v['user_id'] != $operatorId && $operatorName != 'admin') { // 超管可以操作保存和撤销
                    throw new InvalidRequestException("操作失败，原因：【PR单{$id}当前操作员为{$operatorName}不是PR单的上传人员，请核对！】");
                }
            }
            foreach ($data as $v) {
                $temp = $tempAll[$v['id']];
                if (Arr::get($v, 'quantity')) {
                    $temp['quantity'] = $v['quantity'];
                }
                if (Arr::get($v, 'requireDate')) {
                    $temp['require_date'] = $v['requireDate'];
                }
                if (Arr::get($v, 'remark')) {
                    $temp['remark'] = $v['remark'];
                }
                if (Arr::get($v, 'noOrderReason')) {
                    $temp['no_order_reason'] = $v['noOrderReason'];
                }
                $update[] = $temp;
            }
            PrUploadSkusList::query()->upsert($update, ['id']);
        } else {
            foreach ($prInfo as $id => $v) {
                if ($v['status'] != 0) {
                    throw new InvalidRequestException("操作失败，原因：【PR单{$id}的状态必须为待处理，请核对！】");
                }
                if ($v['user_id'] != $operatorId && $operatorName != 'admin') { // 超管可以操作保存和撤销
                    throw new InvalidRequestException("操作失败，原因：【PR单{$id}当前操作员为{$operatorName}不是PR单的上传人员，请核对！】");
                }
            }
            $ids = array_column($data, 'id');
            PrUploadSkusList::query()->whereIn('id', $ids)->update(['status' => PrUploadSkusList::cancel]);
            PmsService::cancelPr($ids); // PMS取消计划单
            if ($isRelease) { // 进销存释放PR（重新复制PR单到数据库）
                self::releasePr($ids);
            }
        }
        return '操作成功';
    }

    /**
     * 释放PR单
     * @param  array  $prIds
     * @author dwj
     */
    public static function releasePr(array $prIds)
    {
        $oldPrInfos = PrUploadSkusList::query()
            ->whereIn('id', $prIds)
            ->select()
            ->get();
        $update = [];
        foreach ($oldPrInfos as $oldPrInfo) {
            $update[] = [
                'upload_files_id'      => $oldPrInfo['upload_files_id'],
                'sku'                  => $oldPrInfo['sku'],
                'quantity'             => $oldPrInfo['quantity'],
                'warehouseid'          => $oldPrInfo['warehouseid'],
                'user_id'              => $oldPrInfo['user_id'],
                'user_nick'            => $oldPrInfo['user_nick'],
                'remark'               => $oldPrInfo['remark'],
                'status'               => 0,
                'check_status'         => $oldPrInfo['check_status'],
                'approve_status'       => 0,
                'check_result'         => '',
                'po'                   => 0,
                'require_date'         => $oldPrInfo['require_date'],
                'old_pr_id'            => $oldPrInfo['id'],
                'pms_po_detail_status' => 0,
                'sku_ware_record'      => $oldPrInfo['sku_ware_record'],
                'no_order_reason'      => '',
            ];
        }
        PrUploadSkusList::query()->insert($update);
    }

    /**
     * 更新PR单异常（批量）
     * @param $request
     * @return string
     * @author dwj
     */
    public static function updateError($request)
    {
        $data = $request->input('data.list');
        $ids = array_column($data, 'id');
        $prInfo = PrUploadSkusList::query()
            ->whereIn('id', $ids)
            ->select([
                'id',
                'sku',
                'upload_files_id'
            ])->get();

        $skus = $prInfo->pluck('sku')->unique()->toArray();
        $supplierInfo = SkuBaseService::getSkuSupplierBySkus($skus);
        $purchaserInfo = SkuBaseService::getSkuPurchserBySkus($skus);

        $updateIds = [];
        $updateFileIds = [];
        foreach ($prInfo as $item) {
            $sku = $item->sku;
            $id = $item->id;
            $uploadFilesId = $item->upload_files_id;
            $purchase = $purchaserInfo[$sku]->nick ?? '';
            $supplier = $supplierInfo[$sku] ?? [];
            if (!empty($purchase) && !empty($supplier)) {
                $updateIds[] = $id;
                $updateFileIds[] = $uploadFilesId;
            }
        }

        !empty($updateIds) && PrUploadSkusList::query()
            ->whereIn('id', $updateIds)
            ->update([
                'status' => PrUploadSkusList::noHandle,
                'check_status' => PrUploadSkusList::pass,
                'check_result' => '', // 检测原因及结果重置为空
            ]);
        !empty($updateFileIds) && PrUploadFilesList::query()
            ->whereIn('id', $updateFileIds)
            ->update([
                'check_status' => PrUploadFilesList::pass,
                'data_result'  => '', // 数据校验结果重置为空
            ]);

        $successNum = count($updateIds); // 成功条数
        $totalNum = count($ids); // 传参总条数
        if ($successNum > 0) {
            if ($successNum == $totalNum) {
                $msg = '全部成功';
            } else {
                $msg = "部分成功，成功条数：【{$successNum}】";
            }
        } else {
            throw new InvalidRequestException('全部未更新，没有符合更新条件的PR单，请核对！');
        }
        return $msg;
    }

    /**
     * 同步老系统2021-01-01的PR单到新系统
     * @author dwj
     */
    public static function syncOldSystemPrData()
    {
        $date = '2021-01-01'; // 默认同步2021年的数据
        self::startData($date);
    }


    /**
     * 每隔五分钟同步一下老系统PR单到新系统
     * @author dwj
     */
    public static function syncOldSystemPrData2()
    {
        $date = PrUploadSkusList::query()->where('id','<',PrUploadSkusList::pmsProPrIdStart)
            ->select()->max('created_at'); //  同步数据的起始时间
        !empty($date) && self::startData($date);
    }

    public static function startData($date)
    {
        if (empty($date)){
            return false;
        }
        $date = Carbon::parse($date)->format('Y-m-d');
        $times = Carbon::now()->format('Y-m-d H:i:s');
        Log::info("同步进销存老系统PR单数据到新系统 || 开始");
        $i = 0;

        Log::info();

        DB::connection('hz')->table('upload_batch_skus')
            ->select()
            ->where('update_time','>=',$date)
            ->whereIn('warehouse_id',PoBatchUploadImport::warehouseId) // 只同步国内仓的PR单
            ->chunkById(3000,function ($items) use (&$i){
                $i += count($items);
                // 获取新系统用户id和名称
                $userNicks = array_column($items->toArray(),'user_nick');
                $userInfos = User::query()->whereIn('nickname',$userNicks)
                    ->pluck('id','nickname');

                $update = [];
                foreach ($items as $item){
                    if ($item->id > PrUploadSkusList::pmsProPrIdStart){
                        continue; // 排除脏数据
                    }
                    // 状态映射
                    $status = $item->status;
                    $pmsDetailSkuStatus = $item->pms_po_detail_status;
                    if ($pmsDetailSkuStatus > 0) {
                        $status = PrUploadSkusList::pmsPoDetailStatusShine[$pmsDetailSkuStatus] ?? $status;
                    }
                    $update[] = [
                        'id'                   => $item->id,
                        'upload_files_id'      => $item->upload_batch_id,
                        'sku'                  => $item->sku,
                        'quantity'             => $item->quantity,
                        'warehouseid'          => ($item->warehouse_id == 6) ? 106 : 103,
                        'user_id'              => $userInfos[$item->user_nick] ?? 0,
                        'user_nick'            => $item->user_nick,
                        'remark'               => $item->remark,
                        'status'               => $status,
                        'check_status'         => $item->check_status,
                        'approve_status'       => $item->approve_status,
                        'check_result'         => $item->check_result,
                        'po'                   => $item->pr_no,
                        'require_date'         => $item->require_date,
                        'old_pr_id'            => $item->old_pr_id,
                        'pms_po_detail_status' => $item->pms_po_detail_status,
                        'sku_ware_record'      => $item->sku_ware_record,
                        'no_order_reason'      => $item->no_order_reason,
                        'created_at'           => $item->create_time,
                        'updated_at'           => $item->update_time,
                    ];
                }
                $sql = Formater::sqlInsertAll('pr_upload_skus_lists', $update, [
                    'upload_files_id',
                    'sku',
                    'quantity',
                    'warehouseid',
                    'user_id',
                    'user_nick',
                    'remark',
                    'status',
                    'check_status',
                    'approve_status',
                    'check_result',
                    'po',
                    'require_date',
                    'pms_po_detail_status',
                    'sku_ware_record',
                    'no_order_reason',
                    'created_at',
                    'updated_at',
                ]);
                $sql && DB::insert($sql);
                Log::info("执行完{$i}条");
            });

        $timeEnd = Carbon::now()->format('Y-m-d H:i:s');
        $timeDiff = Carbon::parse($timeEnd)->diffInMinutes($times);
        Log::info("结束 || 花费时间：{$timeDiff}分,执行完{$i}条");
    }

    /**
     * 同步PR单的SKU入库记录字段
     * 规则：新系统的PR单，最近7天创建的PR单
     * @author dwj
     */
    public static function syncSkuWareRecordData()
    {
        $times = Carbon::now()->format('Y-m-d H:i:s');
        Log::info("同步PR单SKU入库标识 || 开始");
        $i = 0;

        $date = Carbon::now()->subDays(7)->format('Y-m-d');
        PrUploadSkusList::query()
            ->where('created_at','>=',$date)
            ->where('id','>=',PrUploadSkusList::pmsProPrIdStart) // 新系统PR单才需要同步
            ->select()
            ->chunkById(3000,function ($items) use (&$i){
                $i += count($items);
                $skus = array_column($items->toArray(),'sku');
                $skuWareRecords = PmsService::getSkuWareRecord($skus);
                $update = [];
                foreach ($items as $item) {
                    $sku = $item->sku;
                    $update[] = [
                        'id'              => $item->id,
                        'sku_ware_record' => $skuWareRecords[$sku] ?? '',
                    ];
                }

                PrUploadSkusList::query()->upsert($update, ['id']);
                //Log::info("执行完{$i}条");
            });

        $timeEnd = Carbon::now()->format('Y-m-d H:i:s');
        $timeDiff = Carbon::parse($timeEnd)->diffInMinutes($times);
        Log::info("结束 || 同步PR单SKU入库标识花费时间：{$timeDiff}分,执行完{$i}条");
    }
}
