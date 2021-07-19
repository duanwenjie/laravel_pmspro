<?php


namespace App\Jobs;

use GuzzleHttp\Exception\ServerException;
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
class LongTimeJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    protected $key;

    /**
     * 任务可以执行的最大秒数 (超时时间)。重要
     *
     * @var int
     */
    public $timeout = 3500;

    //允许异常次数
    public $maxExceptions = 3;

    public function __construct()
    {
        $this->connection = 'redis-long-time';
        $this->queue = 'longTimeJob';
    }


    public function handle()
    {
        try {
        } catch (ServerException $clientException) {
            $this->release(5);
        }
    }


    //直到重试10分钟后
    public function retryUntil()
    {
        return now()->addHour();
    }

    public function failed(Throwable $exception)
    {
        Log::info('给用户发送失败通知'.$exception->getMessage());
    }
}
