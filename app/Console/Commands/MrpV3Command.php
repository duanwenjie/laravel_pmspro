<?php

namespace App\Console\Commands;

use App\Services\MrpBaseData\MrpReportV3Service;
use App\Tools\CacheSet;
use Carbon\Carbon;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Log;

class MrpV3Command extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'mrp_v3';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = '根据原始数据自动补货建议';

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

        if (Cache::get(CacheSet::MRP_V3_FINISH)) {
            Log::info("Mrp V3 has finish");
            return 0;
        }


        Log::info('Mrp V3 Start');

        //todo

        (new MrpReportV3Service())->runData();//v3版本报表及备货需求

        Log::info('Mrp V3 End');

        Cache::put(
            CacheSet::MRP_V3_FINISH,
            Carbon::now()->format('Y-m-d H:i:s'),
            CacheSet::TTL_MAP[CacheSet::MRP_V3_FINISH]
        );
    }
}
