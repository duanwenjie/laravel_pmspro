<?php

namespace App\Console\Commands;

use App\Models\MrpBaseData\MrpBaseOmsSalesList;
use App\Models\MrpBaseData\MrpBaseSkuStockList;
use App\Services\MrpBaseData\AccountService;
use App\Services\MrpBaseData\MrpReportSfService;
use App\Services\MrpBaseData\MrpReportV3Service;
use App\Services\MrpBaseData\OmsService;
use App\Services\MrpBaseData\PmsService;
use App\Services\MrpBaseData\WmsService;
use Illuminate\Console\Command;

class MrpByHandsCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'mrp-by-hands {task}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = '手动重新跑相关数据 php artisan mrp-by-hands base,v3,sf';

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

        //执行
        $taskList = ['base', 'v3', 'sf'];
        $taskInfo = $this->argument('task');
        $tasks = explode(',', $taskInfo);

        foreach ($tasks as $task) {
            if (!in_array($task, $taskList)) {
                $this->error('task 参数有误 只能是 base,v3,sf');
            } else {
                switch ($task) {
                    case 'base':
                        // 第一步，清空表
                        MrpBaseSkuStockList::query()->truncate();
                        MrpBaseOmsSalesList::query()->truncate();
                        // 第二步，同步OMS销量
                        OmsService::syncMrpBaseOmsSalesData();
                        // 第三步，同步PMS数据
                        PmsService::syncMrpBaseSkuPmsData();
                        // 第四步，同步WMS数据
                        WmsService::syncMrpBaseSkuWmsData();
                        // 第五步，同步OMS账号资料数据
                        AccountService::syncBaseAccountListData();
                        break;
                    case 'v3':
                        (new MrpReportV3Service())->runData();//v3版本报表及备货需求
                        break;
                    case 'sf':
                        (new MrpReportSfService())->runData();//sf版本报表及备货需求
                        break;
                }
            }
        }
    }
}
