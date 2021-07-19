<?php

namespace App\Console\Commands;

use App\Models\PrUpload\PrUploadSkuFollowList;
use App\Services\PrUpload\PoBatchUploadService;
use App\Services\PrUpload\SkuFollowService;
use Illuminate\Console\Command;

class PrUploadSyncSkuFollowDataCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'sync_pr_upload_sku_follow_data';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = '同步PR上传SKU跟进表数据&PR单SKU入库记录';

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

        PrUploadSkuFollowList::query()->truncate();

        // 同步跟进表
        SkuFollowService::syncSkuFollowData();

        SkuFollowService::updatePoPcs();

        // 同步SKU入库记录
        PoBatchUploadService::syncSkuWareRecordData();

        return 0;
    }
}
