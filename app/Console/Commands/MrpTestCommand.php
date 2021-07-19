<?php

namespace App\Console\Commands;

use App\Services\MrpBaseData\MrpTest;
use Illuminate\Console\Command;

class MrpTestCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'mrp_test';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = '验证脚本';

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
//        (new MrpTest())->syncHzBaseData();
//        Cache::put(
//            CacheSet::MRP_BASE_FINISH,
//            Carbon::now()->format('Y-m-d H:i:s'),
//            CacheSet::TTL_MAP[CacheSet::MRP_BASE_FINISH]
//        );
//
//        $this->callSilent('mrp_v3');
//        $this->callSilent('mrp_sf');

        (new MrpTest())->validate();
    }
}
