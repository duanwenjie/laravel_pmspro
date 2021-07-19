<?php

namespace App\Console\Commands;

use App\Services\MrpBaseData\MrpReportSfService;
use App\Tools\CacheSet;
use Carbon\Carbon;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Log;

class MrpSfCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'mrp_sf';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = '根据原始数据为海狮自动补货建议（sf)';

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
        if (!Cache::get(CacheSet::MRP_BASE_FINISH)) {
            Log::info("Mrp Base no finish");
            return 0;
        }
        if (Cache::get(CacheSet::MRP_HS_FINISH)) {
            Log::info("Mrp HS has finish");
            return 0;
        }
        Log::info('Mrp HS');
        (new MrpReportSfService())->runData();
        Log::info('Mrp HS End');
        Cache::put(
            CacheSet::MRP_HS_FINISH,
            Carbon::now()->format('Y-m-d H:i:s'),
            CacheSet::TTL_MAP[CacheSet::MRP_HS_FINISH]
        );
    }
}
