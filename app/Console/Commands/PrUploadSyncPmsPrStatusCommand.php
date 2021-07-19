<?php

namespace App\Console\Commands;

use App\Services\PrUpload\PmsService;
use Illuminate\Console\Command;

class PrUploadSyncPmsPrStatusCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'sync_pms_pr_status';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = '同步PMS PR单状态到进销存PR单列表';

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

        PmsService::syncPmsPrStatus();

        return 0;
    }
}
