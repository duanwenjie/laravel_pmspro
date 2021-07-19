<?php


namespace App\Http\Controllers\Api\Server;

use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;

class UserController extends Controller
{
    public function info()
    {
        return $this->success('查询成功', Auth::user());
    }
}
