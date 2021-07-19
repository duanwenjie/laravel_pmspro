<?php


namespace App\Jobs;

use App\Models\User;
use App\Models\UserImportExportRecord;
use Carbon\Carbon;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Auth;
use Throwable;

/**
 * @package App\Jobs
 */
class AsyncImportExportJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    protected $importExportRecord;
    protected $requestParam;
    protected $action;

    /**
     * 任务可以执行的最大秒数 (超时时间)。重要
     *
     * @var int
     */
    public $timeout = 3500;


    public function __construct(UserImportExportRecord $importExportRecord, $requestParam, $action)
    {
        $this->importExportRecord = $importExportRecord;
        $this->action = $action;
        $this->requestParam = $requestParam;
        //$this->connection = 'redis-long-time';
        //$this->queue = 'longTimeJob';
    }

    public function handle()
    {
        Auth::login(User::find($this->importExportRecord->user_id));
        $this->importExportRecord->update([
            'status'       => UserImportExportRecord::STATUS_PENDING,
            'processed_at' => Carbon::now()
        ]);
        try {
            $this->requestParam['import_export_record'] = $this->importExportRecord;
            call_user_func($this->action, $this->requestParam);
        } catch (Throwable $exception) {
            $this->importExportRecord->update(
                [
                    'status'       => UserImportExportRecord::STATUS_FAIL,
                    'result'       => $exception->getMessage(),
                    'completed_at' => Carbon::now()
                ]
            );
        }
    }
}
