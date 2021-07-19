<?php

namespace App\Console\Commands;

use App\Services\PrUpload\PmsService;
use App\Services\PrUpload\PoBatchUploadService;
use Illuminate\Console\Command;

class PrUploadSyncOldPrDataCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'sync_hz_pr_data';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = '同步进销存2021-01-01PR单数据到新系统';

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

        PoBatchUploadService::syncOldSystemPrData();

        return 0;
    }
}
