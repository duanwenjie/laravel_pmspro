<?php

namespace App\Console\Commands;

use App\Exceptions\InternalException;
use App\Models\Mrp\MrpResultPlanSf;
use App\Models\Mrp\MrpResultPlanV3;
use App\Services\MrpBaseData\ReportService;
use App\Tools\CacheSet;
use Carbon\Carbon;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\DB;
use Qian\DingTalk\DingTalk;
use Qian\DingTalk\Message;

class MrpCheckCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'mrp_check';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'mrp 检查是否有执行完成';

    /**
     * Create a new command instance.
     *
     * @return void
     */
    public function __construct()
    {
        parent::__construct();
    }

    /**
     * Execute the console command.
     *
     * @return int
     */
    public function handle()
    {
        //检查值map 的关键节点是否有值
        $mrpBase = Cache::get(CacheSet::MRP_BASE_FINISH);
        $mrpV3 = Cache::get(CacheSet::MRP_V3_FINISH);
        $mrpHs = Cache::get(CacheSet::MRP_HS_FINISH);
        $mrpFinishNotify = Cache::get(CacheSet::MRP_FINISH_NOTIFY);
        if (date('i') > 30) {
            if (!$mrpBase) {
                throw new InternalException('Mpr Base 未执行');
            }
            if (!$mrpV3) {
                throw new InternalException('Mpr V3 未执行');
            }
            if (!$mrpHs) {
                throw new InternalException('Mpr HS 未执行');
            }
        }
        if ($mrpHs && $mrpV3 && !$mrpFinishNotify) {
            //记录下时间
            $max = min(strtotime($mrpHs), strtotime($mrpV3));
            Cache::put(
                CacheSet::MRP_FINISH.Carbon::now()->format('Y-m-d H'),
                date('Y-m-d H:i:s', $max),
                CacheSet::TTL_MAP[CacheSet::MRP_FINISH]
            );
            $v3Count = MrpResultPlanV3::query()->where('confirm_status', '!=', -2)
                ->select([
                    DB::raw('count(1) as num'),
                    DB::raw('sum(replenishment_num) pcs')
                ])->first();
            if ($v3Count) {
                $v3SkuNum = $v3Count->num;
                $v3SkuPcs = $v3Count->pcs;
            } else {
                $v3SkuNum = 0;
                $v3SkuPcs = 0;
            }
            $sfCount = MrpResultPlanSf::query()->where('confirm_status', '!=', -2)
                ->select([
                    DB::raw('count(1) as num'),
                    DB::raw('sum(replenishment_num) pcs')
                ])->first();
            if ($sfCount) {
                $sfSkuNum = $sfCount->num;
                $sfSkuPcs = $sfCount->pcs;
            } else {
                $sfSkuNum = 0;
                $sfSkuPcs = 0;
            }
            $message = new Message();
            $message->at(config('dingtalk.mrp.'.config('app.env').'.at'), false);
            $payload = "【新MRP提醒您数据计算成功】"."\n";
            $payload .= '【批次号】'.(new ReportService())->getComputeBatch()."\n";
            $payload .= '【V3版本】'."SKU个数:{$v3SkuNum},PCS:{$v3SkuPcs}"."\n";
            $payload .= '【海狮版本】'."SKU个数:{$sfSkuNum},PCS:{$sfSkuPcs}"."\n";
            $payload .= '【执行完成时间】'.date('Y-m-d H:i:s', $max);
            (new DingTalk())->send(config('dingtalk.mrp.'.config('app.env').'.token'), $message->text($payload));
            Cache::put(CacheSet::MRP_FINISH_NOTIFY, 1, CacheSet::TTL_MAP[CacheSet::MRP_FINISH_NOTIFY]);
        }
    }
}
