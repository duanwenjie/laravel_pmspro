<?php

namespace App\Console\Commands;

use App\Services\MrpBaseData\OmsService;
use App\Services\MrpBaseData\PmsService;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Log;

class MrpSyncOmsPmsStockCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'mrp_sync_oms_pms_base_data';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = '同步最新的OMS、PMS库存数据';

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
        ini_set('memory_limit', '4096M');

        Log::info('MrpSyncOmsPmsStockCommand start');

        // 跑之前先初始化总未发数量
        PmsService::initOmsStockNum();

        // 获取OMS总未发
        OmsService::getOmsOccupyStockNum();

        Log::info('MrpSyncOmsPmsStockCommand end');

        return 0;
    }
}
