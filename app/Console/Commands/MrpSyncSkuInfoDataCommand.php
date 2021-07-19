<?php

namespace App\Console\Commands;

use App\Services\MrpBaseData\SkuService;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Log;

class MrpSyncSkuInfoDataCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'mrp_sync_sku_info_base_data';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = '同步SKU基础资料数据';

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

        Log::info('MrpSyncSkuInfoDataCommand start');

        // 同步SKU基础资料数据
        SkuService::syncSkuBaseInfos();

        Log::info('MrpSyncSkuInfoDataCommand end');
        return 0;
    }
}
