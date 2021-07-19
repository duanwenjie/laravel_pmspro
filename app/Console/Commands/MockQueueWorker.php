<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\Redis;

class MockQueueWorker extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'mock:queue-worker';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Command description';

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
    public function handle() : int
    {
        set_time_limit(3600);
        ini_set('memory_limit', '2048M');

        $this->info("监听消息队列开始");

        while (true){
            $postId = Redis::rpop('queue_test');
            if ($postId){
                $this->info("更新省市区 #{$postId} 的浏览数");
                sleep(1);
            }
        }
        return 0;
    }
}
