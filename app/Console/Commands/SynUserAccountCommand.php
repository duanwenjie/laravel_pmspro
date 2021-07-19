<?php

namespace App\Console\Commands;

use App\Models\User;
use App\Tools\Client\YksHrClient;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;

class SynUserAccountCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'sync-user-account';


    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = '从人事系统同步用户';

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

        $pageNumber = 1;
        while (true) {
            $param = [
                'pageNumber' => $pageNumber,
                'pageData'   => 100,
            ];
            $list = YksHrClient::getUserList(['data' => $param]);
            if (!$list) {
                break;
            }
            DB::transaction(function () use ($list) {
                $leaveUserIds = [];
                foreach ($list as $_list) {
                    if (!$_list['username']) {
                        continue;
                    }
                    $user = User::query()->where('username', $_list['username'])->first();
                    if ($user) {
                        $user->update([
                            'status'   => $_list['status'],
                            'nickname' => $_list['nameCn'],
                        ]);
                    } else {
                        $user = User::query()->create([
                            'status'   => $_list['status'],
                            'nickname' => $_list['nameCn'],
                            'username' => $_list['username'],
                        ]);
                    }
                    if ($_list['status'] == 2) {
                        $leaveUserIds[] = $user->id;
                    }
                }
            });
            $pageNumber++;
        }
    }
}
