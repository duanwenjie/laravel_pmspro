<?php


namespace App\Jobs;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Log;
use Throwable;

/**
 * @package App\Jobs
 */
class TestJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    protected $key;

    //允许异常次数
//    public $maxExceptions = 3;


    public function __construct($key)
    {
        $this->key = $key;
    }

    public function handle()
    {
        if (true) {
            Log::info('xxxxxx');
        } else {
            $this->release(5);
        }
    }


    //直到重试10分钟后
    public function retryUntil()
    {
        return now()->addMinute();
    }

    public function failed(Throwable $exception)
    {
        Log::info('给用户发送失败通知'.$exception->getMessage());
    }
}
