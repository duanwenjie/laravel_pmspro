<?php

namespace App\Console\Commands;

use App\Models\MrpBaseData\MrpBaseOmsSalesList;
use App\Models\MrpBaseData\MrpBaseSkuStockList;
use App\Services\MrpBaseData\AccountService;
use App\Services\MrpBaseData\MrpTest;
use App\Services\MrpBaseData\OmsService;
use App\Services\MrpBaseData\PmsService;
use App\Services\MrpBaseData\WmsService;
use App\Tools\CacheSet;
use Carbon\Carbon;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Log;

class MrpSyncBaseDataCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'mrp_sync_base_data';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'MRP同步基础数据(OMS销量源数据、OMS总未发、WMS数据、PMS数据)';

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

        if (Cache::get(CacheSet::MRP_BASE_FINISH)) {
            Log::info("Mrp Base has finish");
            return 0;
        }
        Log::info('Mrp Base');
        // 第一步，清空表
        MrpBaseSkuStockList::query()->truncate();

        MrpBaseOmsSalesList::query()->truncate();

        //todo 临时开启等正式启用后关闭
        (new MrpTest())->syncBaseCore();

        // 第二步，同步OMS销量
        OmsService::syncMrpBaseOmsSalesData();

        // 第三步，同步PMS数据
        PmsService::syncMrpBaseSkuPmsData();

        // 第四步，同步WMS数据
        WmsService::syncMrpBaseSkuWmsData();

        // 第五步，同步OMS账号资料数据
        AccountService::syncBaseAccountListData();

        //执行完成后设置一个redis key值
        Log::info('Mrp Base End');
        Cache::put(
            CacheSet::MRP_BASE_FINISH,
            Carbon::now()->format('Y-m-d H:i:s'),
            CacheSet::TTL_MAP[CacheSet::MRP_BASE_FINISH]
        );

        return 0;
    }
}
