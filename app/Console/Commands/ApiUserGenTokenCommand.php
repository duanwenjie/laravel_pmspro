<?php

namespace App\Console\Commands;

use App\Models\ApiUser;
use Illuminate\Console\Command;

class ApiUserGenTokenCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'api-user-gen-token {id?}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'api-user-gen-token';

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
        $id = $this->argument('id');
        $apiUser = ApiUser::find($id);
        if ($apiUser) {
            $token = $apiUser->createToken('pms-pro')->plainTextToken;
            $this->line($token);
        } else {
            $this->warn('用户ID 不存在');
        }
    }
}
