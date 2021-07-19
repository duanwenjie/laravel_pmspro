<?php

namespace App\Console;

use Illuminate\Console\Scheduling\Schedule;
use Illuminate\Foundation\Console\Kernel as ConsoleKernel;

class Kernel extends ConsoleKernel
{
    /**
     * The Artisan commands provided by your application.
     *
     * @var array
     */
    protected $commands = [
        //
    ];

    /**
     * Define the application's command schedule.
     *
     * @param  Schedule  $schedule
     * @return void
     */
    protected function schedule(Schedule $schedule)
    {

        //定时任务非特殊情况不需要开启withoutOverlapping ，若开启，请设置时间withoutOverlapping（10）单位分钟
        // $schedule->command('inspire')->hourly();
        $schedule->command('horizon:snapshot')->everyMinute();
        //每日清理telescope 数据 默认24小时
        $schedule->command('telescope:prune')->daily();
        //同步登录账号
        $schedule->command('sync-user-account')->everyThirtyMinutes();

        //生产环境才需要执行的任务
        if (config('app.env') == 'production') {
            //基础资料信息；跑mrp要提前准备
            $schedule->command('mrp_sync_sku_info_base_data')->cron('30 5,12 * * *')->withoutOverlapping(60);


            //每天的6点或者13点开始执行，每分钟执行一次 防止重复执行执行,10分钟内不允许执行重复
            $cron = '* 6,13 * * *';
            $schedule->command('mrp_sync_base_data')->cron($cron)->withoutOverlapping(10);
            $schedule->command('mrp_v3')->cron($cron)->withoutOverlapping(10);
            $schedule->command('mrp_sf')->cron($cron)->withoutOverlapping(10);
            $schedule->command('mrp_check')->cron($cron)->withoutOverlapping(10);

            $schedule->command('mrp_other_report')->cron('50 6,13 * * *');


            //每天 8-12 点 14-23点 隔5钟抓一次oms 总未发
            $schedule->command('mrp_sync_oms_pms_base_data')->cron('*/5 8-12,14-23 * * *');
            //每30钟抓一次 采购在途
            $schedule->command('mrp_sync_oms_pms_base_data2')->cron('*/30 * * * *');
        }else{

        }

        // 每隔5五分钟同步进销存PR单状态到PMS
        $schedule->command('sync_pms_pr_status')->cron('*/5 * * * *');

        // 每隔5五分钟同步进销存PR单数据到PMSPRO
        $schedule->command('sync_hz_pr_data2')->cron('*/5 * * * *');

        // 每天凌晨三点同步PR SKU跟进表数据
        $schedule->command('sync_pr_upload_sku_follow_data')->cron('1 3 * * *');
    }

    /**
     * Register the commands for the application.
     *
     * @return void
     */
    protected function commands()
    {
        $this->load(__DIR__.'/Commands');

        require base_path('routes/console.php');
    }
}
