<?php
/**
 * MRP其他类型报表
 * User: jiangshilin(jiangshilin@youkeshu.com)
 * Date: 2021/5/22
 * Time: 17:12
 */


namespace App\Console\Commands;

use App\Services\MrpBaseData\MrpOtherReport1Service;
use App\Services\MrpBaseData\MrpOtherReportService;
use App\Tools\CacheSet;
use Carbon\Carbon;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Log;
use Qian\DingTalk\DingTalk;
use Qian\DingTalk\Message;

class MrpOtherReportCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'mrp_other_report';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'MRP模块其他类型报表（计算补货不依赖，只做计划部业务支撑）生成';

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
        set_time_limit(3600);
        ini_set('memory_limit', '2048M');
        //判断基础数据拉取是否执行完。

        if (!Cache::get(CacheSet::MRP_V3_FINISH)) {
            Log::info("Mrp V3 no finish");
            return 0;
        }

        if (Cache::get(CacheSet::MRP_OTHER_REPORT_FINISH)) {
            Log::info("Mrp OTHER has finish");
            return 0;
        }

        Log::info('Mrp OTHER');

        (new MrpOtherReportService())->runData();//MRP其他类型报表

        Log::info('Mrp OTHER End');

        $now = Carbon::now()->format('Y-m-d H:i:s');
        Cache::put(
            CacheSet::MRP_OTHER_REPORT_FINISH,
            $now,
            CacheSet::TTL_MAP[CacheSet::MRP_OTHER_REPORT_FINISH]
        );

        $message = new Message();
        $message->at(config('dingtalk.mrp.'.config('app.env').'.at'), false);
        $payload = "【新MRP提醒您报表计算成功】"."\n";
        $payload .= '【执行完成时间】'.$now;
        (new DingTalk())->send(config('dingtalk.mrp.'.config('app.env').'.token'), $message->text($payload));
    }
}
